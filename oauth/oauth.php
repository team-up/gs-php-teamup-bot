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
	public function getToken($forceUpdate = FALSE) {
		$token = $this->token;
		if ($token->accessToken === NULL) {
			$token->setToken(self::password());
		} elseif (time() >= $token->expiresIn || $forceUpdate) {
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
		$headers = self::parseHeaders($http_response_header);
		$responseCode = $headers['response_code'];
		// Status code가 200이 아닐경우 에러 throw
		if ($responseCode !== '200') {
			throw new AuthException('Auth error :'.$url, $responseCode);
		}
		$response = json_decode($result);
		if (empty($response) || empty($response->access_token)) {
			$msg = 'Unknown error';
			if (isset($response->error)) {
				$msg = $response->error;
			}
			throw new AuthException('Auth error : '.$msg, 200);
		}
		return $response;
	}
	
	private function parseHeaders($headers) {
		$head = array('response_code' => 0);
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