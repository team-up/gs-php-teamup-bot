<?php
class Token {
	public $accessToken;
	public $refreshToken;
	public $expiresIn;
	public $tokenType;
	public static function getInstance() {
		static $instance = NULL;
		if ($instance === NULL) {
			$instance = new Token();
		}
		return $instance;
	}
	
	public function setToken($token) {
		$this->accessToken = $token->access_token;
		$this->refreshToken = $token->refresh_token;
		$this->tokenType = $token->token_type;
		$this->expiresIn = time() + $token->expires_in - 60 * 5;
	}
}