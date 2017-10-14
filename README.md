# Discord News bot

General "news" bot which automatically reads and posts news from different news channels to a Discord channel.

Currently the goal is to get it to work with Twitter for the game called PUBG.

##  Installation
 * Download repository and run composer update.
 * Replace "service_description-v6.json" in ./vendor/restcord/restcord/src/Resources/ with the one provided in the root of this repository (replaces snowflake with string).
 * Make sure the PHP script has read/write access to the messages.json file.
 * Rename or copy config.php.default to config.php and enter your own configuration

## News channels
 * Twitter: https://twitter.com/PUBATTLEGROUNDS / https://twitter.com/PLAYERUNKNOWN
 * Twitch.tv: https://www.twitch.tv/playbattlegrounds
 * Steam: http://store.steampowered.com/news/?appids=578080

## Todo

### Steam
API?
Webhooks?

### YouTube
