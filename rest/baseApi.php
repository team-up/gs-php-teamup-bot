<?php
if (!defined('BASE_ROOT')) {
    define('BASE_ROOT', dirname(__DIR__));
}
require_once BASE_ROOT.'/include.php';
require_once BASE_ROOT.'/oauth/oauth.php';
class baseApi {
	private $headers;
	protected $timeout = 5;
	private $oauth;
	public function __construct() {
		$this->oauth = new OAuth();
	}
	protected function get($url, $params = array()) {
		if ($params) {
			$url .= "?".http_build_query($params);
		}
		return self::call($url);
	}
	protected function post($url, $params = array()) {
		return self::call($url, $params, 'POST');
	}
	private function call($url, $params = NULL, $method = NULL) {
		$header = "Content-type: application/json\r\n";
		$header .= "Authorization: {$this->oauth->getToken()}\r\n";
		$options = array('http' => array(
			'header' => $header,
			'timeout' => $this->timeout
		));
		if (!empty($params)) {
			$options['http']['content'] = json_encode($params);
		}
		if ($method) {
			$options['http']['method'] = $method;
		}
		$context = stream_context_create($options);
		$result = file_get_contents($url, FALSE, $context);
		if (empty($http_response_header)) {
			throw new Exception('HTTP request failed');
		}
		$this->headers = self::parseHeaders($http_response_header);
		// Status code가 2XX가 아닐경우 에러 throw
		if ($this->headers['response_code'][0] !== '2') {
			throw new Exception('API Error: '.$this->headers['response_code']." URL:".$url);
		}
		return json_decode($result);
	}
	private function parseHeaders($headers) {
		$head = array();
		foreach ($headers as $key => $value) {
			$tmp = explode(':', $value, 2);
			if	(isset($tmp[1])) {
				$head[trim($tmp[0])] = trim($tmp[1]);
			} else {
				$head[] = $value;
				if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $value, $out)) {
					$head['response_code'] = $out[1];
				}
			}
		}
		return $head;
	}
}