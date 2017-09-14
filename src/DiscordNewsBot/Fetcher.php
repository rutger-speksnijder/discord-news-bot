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
}
