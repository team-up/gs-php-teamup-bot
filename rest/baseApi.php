<?php
if (!defined('BASE_ROOT')) {
    define('BASE_ROOT', dirname(__DIR__));
}
require_once BASE_ROOT.'/include.php';
require_once BASE_ROOT.'/oauth/oauth.php';
class baseApi {
	private $headers;
	private $timeout = 5;
	private $oauth;
	public function __construct() {
		$this->oauth = new OAuth();
	}
	protected function get($url, $params = array(), $options = array()) {
		if ($params) {
			$url .= "?".http_build_query($params);
		}
		$options['http']['method'] = 'GET';
		return self::call($url, NULL, $options);
	}
	protected function post($url, $params = array(), $options = array()) {
		$options['http']['method'] = 'POST';
		return self::call($url, $params, $options);
	}
	private function call($url, $params = NULL, $options = array()) {
		$header = "Content-type: application/json\r\n";
		$header .= "Authorization: {$this->oauth->getToken()}\r\n";
		$options['http']['header'] = $header;
		if (empty($options['http']['timeout'])) {
			$options['http']['timeout'] = $this->timeout;
		}
		if (!empty($params)) {
			$options['http']['content'] = json_encode($params);
		}
		$context = stream_context_create($options);
		$result = file_get_contents($url, FALSE, $context);
		if (empty($http_response_header)) {
			throw new Exception('HTTP request failed');
		}
		$this->headers = self::parseHeaders($http_response_header);
		$responseCode = $this->headers['response_code'];
		// Status code가 2XX가 아닐경우 에러 throw
		if ($responseCode[0] !== '2') {
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