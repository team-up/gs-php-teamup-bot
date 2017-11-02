<?php
if (!defined('BASE_ROOT')) {
    define('BASE_ROOT', dirname(__DIR__));
}
require_once BASE_ROOT.'/include.php';
require_once BASE_ROOT.'/rest/baseApi.php';
class Edge extends baseAPi {
	private $url = EDGE_API_URL;
}