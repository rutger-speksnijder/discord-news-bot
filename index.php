<?php
// Composer autoloader
require 'vendor/autoload.php';

// Temporary enable errors
error_reporting(E_ALL);
ini_set('display_errors', 'on');

use RestCord\DiscordClient;

// Load the config
$config = require 'config.php';

// Create the client
$discord = new RestCord\DiscordClient(['token' => $config['bot']['token']]);

// Create a sample message
$discord->channel->createMessage([
    'channel.id' => $config['news']['twitter'][0]['channel'],
    'content' => '@PUBATTLEGROUNDS (https://twitter.com/PUBATTLEGROUNDS) just tweeted: https://twitter.com/PUBATTLEGROUNDS/status/906336994275610627',
]);
