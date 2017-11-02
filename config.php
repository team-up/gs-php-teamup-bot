<?php
$config = parse_ini_file('/data/etc/teamup-bot-php/config.ini', TRUE);
if (!$config) {
	die('Config file is required.');
}
define('CLIENT_ID', $config['client']['id']);
define('CLIENT_SECRET', $config['client']['secret']);
define('USERNAME', $config['user']['id']);
define('PASSWORD', $config['user']['pw']);