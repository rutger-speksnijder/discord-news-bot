<?php
// Composer autoloader
require 'vendor/autoload.php';

// Project autoloader
require 'autoload.php';

// Temporary enable errors
error_reporting(E_ALL);
ini_set('display_errors', 'on');

// Define application root
define('ROOT', dirname(__FILE__));

// Load the config
$config = require 'config.php';

// Create the bot
$bot = new DiscordNewsBot\DiscordNewsBot($config);
$bot->run();
