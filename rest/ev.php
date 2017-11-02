<?php
if (!defined('BASE_ROOT')) {
    define('BASE_ROOT', dirname(__DIR__));
}
require_once BASE_ROOT.'/include.php';
require_once BASE_ROOT.'/rest/baseApi.php';
class Ev extends baseAPi {
	private $url = EV_API_URL;
	public function getApiInfo() {
		return self::get(EV_API_URL);
	}
	public function getEvent($timeout) {
		$options['http']['timeout'] = $timeout;
		return self::get(EV_API_URL.'/v3/events', NULL, $options);
	}
}