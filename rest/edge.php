<?php
require_once BASE_ROOT.'/include.php';
require_once BASE_ROOT.'/rest/baseApi.php';

class Edge extends baseAPi {
	private $url = EDGE_API_URL;
	public function getMessage($room, $msg) {
		$data['all'] = 1;
		return self::get(EDGE_API_URL."/message/summary/{$room}/{$msg}", $data);
	}
	public function createMessage($room, $data) {
		return self::post(EDGE_API_URL."/message/${room}", $data);
	}
}