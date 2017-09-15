<?php
namespace DiscordNewsBot;

/**
 * Base class to create and run the news bot.
 *
 * @author Rutger Speksnijder.
 * @since DiscordNewsBot 0.1.
 */
class DiscordNewsBot
{
    /**
     * The configuration array.
     * @var array.
     */
    private $config;

    /**
     * The processes.
     * @var array.
     */
    private $processes;

    /**
     * The known providers.
     * @var array.
     */
    private $providers = [
        'Twitter' => [
            'process' => 'single',
        ],
        'Twitch' => [
            'process' => 'single',
        ],
    ];

    /**
     * The Discord client.
     * @var Restcord\DiscordClient.
     */
    private $discord;

    /**
     * Constructs a new instance of the DiscordNewsBot class.
     *
     * @param array $config The configuration array.
     * @see config.php.default file.
     */
    public function __construct($config)
    {
        if (DIRECTORY_SEPARATOR == "\\") {
            die('Unfortunately, this program will not run under Windows.');
        }

        $this->config = $config;
        $this->processes = [];
        $this->validateConfig();

        $this->discord = new \RestCord\DiscordClient(['token' => $this->config['bot']['token']]);
    }

    /**
     * Destroys all fetch processes upon destruction.
     */
    public function __destruct()
    {
        // Loop through the processes
        foreach ($this->processes as $process) {
            // Check if the process is running
            $result = shell_exec(sprintf('ps %d', $process));
            if (count(explode("\n", $result)) <= 2) {
                continue;
            }

            // Kill the process
            shell_exec(sprintf('kill %d', $process));
        }
    }

    /**
     * Validates the configuration array.
     *
     * @throws Exception Throws an exception for invalid configuration arrays.
     *
     * @return void.
     */
    private function validateConfig()
    {
        // Check if a token is set
        if (empty($this->config['bot']['token'])) {
            throw new \Exception("Unable to start DiscordNewsBot. A bot token is missing.");
        }

        // Check if no providers are set
        if (empty($this->config['providers'])) {
            throw new \Exception("Unable to start DiscordNewsBot. You must set at least one provider.");
        }
    }

    /**
     * Starts the bot's cycle.
     *
     * @return void.
     */
    public function run()
    {
        $this->initializeProviders();
        $this->loop();
    }

    /**
     * Initializes the providers and their fetching processes.
     *
     * @throws Exception Throws an exception when trying to initialize an unknown provider.
     *
     * @return void.
     */
    private function initializeProviders()
    {
        // Loop through the providers
        foreach ($this->config['providers'] as $name => $data) {
            // Check if the provider exists
            if (!isset($this->providers[$name])) {
                throw new \Exception("Unable to start DiscordNewsBot. Unknown provider \"{$name}\".");
            }

            // Check if we need to load multiple processes for this provider
            if ($this->providers[$name]['process'] == 'multiple') {
                die('implement multiple processes');
                continue;
            }

            // Set the command
            $fetchProcess = ROOT . '/src/DiscordNewsBot/fetch-process.php';
            $command = 'php ' . escapeshellarg($fetchProcess) . ' ' . escapeshellarg($name) . ' > /dev/null 2>&1 & echo $!';

            // Start the process and add the process id to the array
            $this->processes[] = shell_exec($command);
        }
    }

    /**
     * Main bot cycle which checks for new messages and sends them to the Discord API.
     *
     * @return void.
     */
    private function loop()
    {
        // Loop until program exists
        while (true) {
            // Get the data
            $data = file_get_contents(ROOT . '/messages.json');
            if (!$data) {
                sleep(1);
                continue;
            }

            // Decode the data
            $messages = json_decode($data, true);
            foreach ($messages as $message) {
                // Send the message
                $this->discord->channel->createMessage([
                    'channel.id' => $message['channel'],
                    'content' => $message['message'],
                ]);
            }

            // Clear the messages
            file_put_contents(ROOT . '/messages.json', '');

            // Keep looping
            sleep(1);
        }
    }
}
