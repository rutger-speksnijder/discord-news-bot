<?php
// Define application root
define('ROOT', dirname(dirname(dirname(__FILE__))));

// Composer autoloader
require 'vendor/autoload.php';

// Project autoloader
require 'autoload.php';

// Set the provider name
$providerName = $argv[1] ?? '';
$class = "\\DiscordNewsBot\\Providers\\{$providerName}\\Fetcher";
if (!$providerName
    || !class_exists($class)
    || !is_subclass_of($class, "\\DiscordNewsBot\\Fetcher")
) {
    throw new \Exception("Unable to start fetch process. Unknown provider \"{$providerName}\".");
}

// Load the config
$config = require ROOT . '/config.php';

// Check if the provider exists in the config
if (empty($config['providers'][$providerName])) {
    throw new \Exception("Unable to start fetch process. Missing provider configuration.");
}

// Create and run the fetcher
$fetcher = new $class($config['providers'][$providerName]['config'], $config['providers'][$providerName]['streams']);
$fetcher->run();
