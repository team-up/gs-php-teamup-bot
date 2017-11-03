<?php
if (!defined('BASE_ROOT')) {
    define('BASE_ROOT', dirname(__FILE__));
}
require_once BASE_ROOT.'/bot/textBot.php';
require_once BASE_ROOT.'/bot/buttonBot.php';
switch (BOT_TYPE) {
	case 'text':
		$bot = new TextBot();
		break;
	case 'button':
		$bot = new ButtonBot();
		break;
	default:
		$bot = new TextBot();
}
$bot->run();