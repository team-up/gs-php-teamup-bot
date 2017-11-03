<?php
$config = parse_ini_file('/data/etc/teamup-bot-php/config.ini', TRUE);
if (!$config) {
	throw new Exception('Config file is required.');
}
define('CLIENT_ID', $config['client']['id']);
define('CLIENT_SECRET', $config['client']['secret']);
define('USERNAME', $config['user']['id']);
define('PASSWORD', $config['user']['pw']);
define('BOT_TYPE', $config['bot']['type']);
