<?php
if (!defined('BASE_ROOT')) {
    define('BASE_ROOT', dirname(__DIR__));
}
require_once BASE_ROOT.'/include.php';
require_once BASE_ROOT.'/oauth/token.php';
class OAuth {
	private $url = AUTH_API_URL;
	private $token;
	public function __construct() {
		$this->token = Token::getInstance();
	}
	public function getToken() {
		$token = $this->token;
		if ($token->accessToken === NULL) {
			$token->setToken(self::password());
		} elseif (time() >= $token->expiresIn) {
			try {
				$token->setToken(self::refresh());
			} catch (Exception $e) {
				$token->setToken(self::password());
			}
		}
		return "{$token->tokenType} {$token->accessToken}";
	}
	private function refresh() {
		$data = array(
			'grant_type' => 'refresh_token',
			'refresh_token' => $this->token->refreshToken
		);
		return self::getAccessToken($data);
	}
	private function password() {
		$data = array(
			'grant_type' => 'password',
			'client_id' => CLIENT_ID,
			'client_secret' => CLIENT_SECRET,
			'username' => USERNAME,
			'password' => PASSWORD
		);
		return self::getAccessToken($data);
	}
	private function getAccessToken($data) {
		$url = $this->url.'/oauth2/token';
		$options = array('http' => array(
			'header' => "Content-type: application/x-www-form-urlencoded\r\n",
			'method' => 'POST',
			'content' => http_build_query($data)
		));
		$context = stream_context_create($options);
		$result = file_get_contents($url, FALSE, $context);
		$this->headers = self::parseHeaders($http_response_header);
		if (empty($http_response_header)) {
			throw new Exception('HTTP request failed');
		}
		// Status code가 2XX가 아닐경우 에러 throw
		if ($this->headers['response_code'][0] !== '2') {
			throw new Exception('API Error: '.$this->headers['response_code']." URL:".$url);
		}
		$response = json_decode($result);
		if (empty($response) || empty($response->access_token)) {
			throw new Exception('Auth error');
		}
		return $response;
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