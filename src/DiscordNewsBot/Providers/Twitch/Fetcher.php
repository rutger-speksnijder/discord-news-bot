<?php
namespace DiscordNewsBot\Providers\Twitch;

/**
 * The fetcher class for the Twitch provider.
 *
 * @author Rutger Speksnijder.
 * @since DiscordNewsBot 0.1.
 */
class Fetcher extends \DiscordNewsBot\Fetcher
{
    /**
     * The Twitch API object.
     * @var \TwitchApi\TwitchApi.
     */
    private $twitchApi;

    /**
     * Whether this is the first loop or not.
     * @var boolean.
     */
    private $firstLoop;

    /**
     * Validates the config and streams data.
     *
     * @throws Exception Throws an exception for invalid data.
     *
     * @return void.
     */
    protected function validate()
    {
        // Check if we have the required fields
        if (empty($this->config['client-id'])) {
            throw new \Exception("Unable to create Twitch fetcher. Missing required configuration data.");
        }

        // Check if the streams are empty
        if (empty($this->streams)) {
            throw new \Exception("Unable to create Twitch fetcher. No streams supplied.");
        }
    }

    /**
     * Starts te process of retrieving stream data.
     *
     * @return void.
     */
    public function run()
    {
        // Initialize the Twitch API object
        $this->twitchApi = new \TwitchApi\TwitchApi(['client_id' => $this->config['client-id']]);

        // Set first loop to true
        $this->firstLoop = true;

        // Loop until the program exists
        while (true) {
            // Check if this is the first loop
            // - During the first loop we just check whether the user is streaming or not
            // - and set their current status in the streams array
            if ($this->firstLoop) {
                // Loop through our streams
                foreach ($this->streams as $key => $stream) {
                    // Get the result from the Twitch API
                    $result = $this->twitchApi->getStreamByUser($stream['userId']);
                    $this->streams[$key]['streaming'] = !empty($result['stream']);
                }

                // Set first loop to false and continue to the next loop
                $this->firstLoop = false;
                continue;
            }

            // Loop through our streams
            foreach ($this->streams as $key => $stream) {
                // Get the result from the Twitch API
                $result = $this->twitchApi->getStreamByUser($stream['userId']);

                // Check if the user started streaming and wasn't previously streaming
                if (!empty($result['stream']) && !$stream['streaming']) {
                    // Set streaming to true
                    $this->streams[$key]['streaming'] = true;

                    // Add the message
                    $this->addMessage(
                        $stream['channel'],
                        $stream['online-message'],
                        [
                            '[title]' => $result['stream']['channel']['status'],
                        ]
                    );

                    // Continue to the next stream
                    continue;
                }

                // Check if the user stopped streaming and was previously streaming
                if (empty($result['stream']) && $stream['streaming']) {
                    // Set streaming to false
                    $this->streams[$key]['streaming'] = false;

                    // Add the message
                    $this->addMessage(
                        $stream['channel'],
                        $stream['offline-message']
                    );

                    // Continue to the next stream
                    continue;
                }
            }

            // Sleep 5 minutes
            sleep(300);
        }
    }
}
