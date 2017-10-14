<?php
namespace DiscordNewsBot\Providers\Twitter;

/**
 * Fetcher class for the Twitter provider.
 *
 * @author Rutger Speksnijder.
 * @since DiscordNewsBot 0.1.
 */
class Fetcher extends \DiscordNewsBot\Fetcher
{
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
        if (empty($this->config['access-token'])
            || empty($this->config['access-token-secret'])
            || empty($this->config['consumer-key'])
            || empty($this->config['consumer-secret'])
        ) {
            throw new \Exception("Unable to create Twitter fetcher. Missing required configuration data.");
        }

        // Check if the streams are empty
        if (empty($this->streams)) {
            throw new \Exception("Unable to create Twitter fetcher. No streams supplied.");
        }
    }

    /**
     * Starts the process of getting data from this provider's streams.
     *
     * @return void.
     */
    public function run()
    {
        // Initialize the public stream
        $publicStream = \Spatie\TwitterStreamingApi\PublicStream::create(
            $this->config['access-token'],
            $this->config['access-token-secret'],
            $this->config['consumer-key'],
            $this->config['consumer-secret']
        );

        // Loop through our streams
        $userIds = [];
        foreach ($this->streams as $stream) {
            // Add the id to the user ids array
            $userIds[] = $stream['id'];
        }

        // Add the listener to the public stream
        $publicStream->whenTweets($userIds, function($tweet) {
            $this->parseTweet($tweet);
        });

        // Start listening to the stream
        $publicStream->startListening();
    }

    /**
     * Parses a tweet and stores it in the json file.
     *
     * @param array $tweet The tweet data received.
     *
     * @return void.
     */
    private function parseTweet(array $tweet)
    {
        // Check if this is a reply, quote or a retweet
        if (!empty($tweet['in_reply_to_status_id'])
            || !empty($tweet['in_reply_to_user_id'])
            || !empty($tweet['in_reply_to_screen_name'])
            || !empty($tweet['retweeted_status'])
            || !empty($tweet['quoted_status_id'])
            || !empty($tweet['quoted_status'])
        ) {
            return;
        }

        // Get the stream this tweet is connected to
        $stream = $this->getStreamFromUserId($tweet['user']['id_str']);
        if (!$stream) {
            return;
        }

        // Loop through the stream's messages
        foreach ($stream['messages'] as $message) {
            // Add the message
            $this->addMessage(
                $message['channel'],
                $message['message'],
                [
                    '[link]' => "https://twitter.com/{$tweet['user']['screen_name']}/status/{$tweet['id_str']}",
                ]
            );
        }
    }

    /**
     * Gets the stream data from a user id.
     *
     * @param string $userId The Twitter user id.
     *
     * @return arrayt The stream data, empty array if none found.
     */
    private function getStreamFromUserId($userId)
    {
        foreach ($this->streams as $stream) {
            if ($stream['id'] == $userId) {
                return $stream;
            }
        }
        return [];
    }
}
