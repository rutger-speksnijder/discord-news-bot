<?php
namespace DiscordNewsBot;

/**
 * Base fetcher class every fetcher should extend from.
 *
 * @author Rutger Speksnijder.
 * @since DiscordNewsBot 0.1.
 */
abstract class Fetcher
{
    /**
     * The configuration array.
     * @var array.
     */
    protected $config;

    /**
     * The streams array.
     * @var array.
     */
    protected $streams;

    /**
     * Constructs a new instance of the fetcher class.
     *
     * @param array $config The configuration array.
     * @param array $streams The streams array.
     */
    final public function __construct($config, $streams)
    {
        $this->config = $config;
        $this->streams = $streams;
        $this->validate();
    }

    /**
     * This method should validate the config and streams based on the requirements by the specific fetcher.
     *
     * @return void.
     */
    abstract protected function validate();

    /**
     * This method should start the fetching process for the current provider.
     *
     * @return void.
     */
    abstract public function run();

    /**
     * Adds a message to the messages file.
     *
     * @param string $channel The Discord channel this message is for.
     * @param string $format The message format.
     * @param optional array $fields The fields to replace.
     *
     * @return void.
     */
    final protected function addMessage($channel, $format, $fields = [])
    {
        // Get the current message data
        $data = file_get_contents(ROOT . '/messages.json');
        $messages = json_decode($data, true);
        if ($messages === null) {
            $messages = [];
        }

        // Format the message
        $message = $format;
        foreach ($fields as $key => $value) {
            $message = str_replace($key, $value, $message);
        }

        // Add the message to the messages array
        $messages[] = [
            'channel' => $channel,
            'message' => $message,
        ];

        // Store the messages
        file_put_contents(ROOT . '/messages.json', json_encode($messages));
    }
}
