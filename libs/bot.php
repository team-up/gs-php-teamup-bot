<?php
if (!defined('BASE_ROOT')) {
    define('BASE_ROOT', dirname(__DIR__));
}
require_once BASE_ROOT."/rest/ev.php";

class Bot {
	private $ev;
	private $lpWaitTime;
	private $lpIdleTime;
	public function __construct() {
		$this->ev = new Ev();
		$data = $this->ev->getApiInfo();
		if (empty($data) || !isset($data->lp_wait_timeout) || !isset($data->lp_idle_time)) {
			throw new Exception("Couldn't get EV API data");
		}
		$this->lpWaitTime = $data->lp_wait_timeout + 5;
		$this->lpIdleTime = $data->lp_idle_time;
	}
	public function run() {
		while (TRUE) {
			try {
				self::longPoll();
			} catch (Exception $e) {
				print($e);
				sleep(1);
			}
		}
	}
	public function longPoll() {
		try {
			$data = $this->ev->getEvent($this->lpWaitTime);
			print_r($data);
		} catch (Exception $e) {
			print($e);
		}
		if (empty($data)) {
			sleep($this->lpIdleTime);
			return ;
		}
	}
}