<?php
require_once BASE_ROOT.'/include.php';
require_once BASE_ROOT.'/rest/baseApi.php';

class Edge extends baseAPi {
	private $url = EDGE_API_URL;
	public function getMessage($room, $msg) {
		return self::get(EDGE_API_URL."/message/summary/{$room}/{$msg}");
	}
	public function getLongMessage($room, $msg) {
		return self::get(EDGE_API_URL."/message/${room}/${msg}");
	}
	public function createMessage($room, $content) {
		$data['content'] = $content;
		return self::post(EDGE_API_URL."/message/${room}", $data);
	}
}