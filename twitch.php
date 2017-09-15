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


$twitchApi = new \TwitchApi\TwitchApi(['client_id' => $config['providers']['Twitch']['config']['client-id']]);


// 24848220
$stream = $twitchApi->getStreamByUser(24848220);
var_dump($stream);
