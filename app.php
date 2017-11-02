<?php
if (!defined('BASE_ROOT')) {
    define('BASE_ROOT', dirname(__FILE__));
}
require_once BASE_ROOT.'/libs/bot.php';
$bot = new Bot();
$bot->run();