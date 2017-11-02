<?php
if (!defined('BASE_ROOT')) {
    define('BASE_ROOT', __DIR__);
}
require_once BASE_ROOT.'/rest/ev.php';
$ev = new Ev();
while (TRUE) {
	try {
		print_r($ev->getEvent());
	} catch(Exception $e) {
		print($e);
		sleep(2);
	}
}