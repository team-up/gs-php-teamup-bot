<?php
require_once BASE_ROOT."/rest/ev.php";
require_once BASE_ROOT."/rest/edge.php";

abstract class BaseBot {
	protected $ev;
	protected $edge;
	protected $lpWaitTime;
	protected $lpIdleTime;
	protected $errorCount = 0;
	public function __construct() {
		$this->ev = new Ev();
		$this->edge = new Edge();
		$data = $this->ev->getApiInfo();
		if (empty($data) || !isset($data->lp_wait_timeout) || !isset($data->lp_idle_time)) {
			throw new Exception("Couldn't get EV API data");
		}
		$this->lpWaitTime = $data->lp_wait_timeout + 5;
		$this->lpIdleTime = $data->lp_idle_time;
	}
	public function run() {
		while (TRUE) {
			$this->longPoll();
		}
	}
	protected function longPoll() {
		try {
			$data = $this->ev->getEvent($this->lpWaitTime);
			$this->errorCount = 0;
			if (empty($data) || empty($data->events)) {
				sleep($this->lpIdleTime);
				return ;
			}
			$this->handleEvent($data->events);
			return ;
		} catch (ApiException $e) {
			error_log($e);
			$this->errorCount += 1;
			$statusCode = $e->getCode();
			// 400번대 에러, API 에러가 10번 연속 발생 시 프로그램 종료
			if (intval($statusCode / 100) === 4 || $this->errorCount >= 10) {
				throw $e;
			}
		} catch (AuthException $e) {
			error_log($e);
			$this->errorCount += 1;
			$statusCode = $e->getCode();
			// 400 번대, Auth API에서 error JSON 반환 시, Auth 에러가 5번 연속 발생 시 프로그램 종료
			if (intval($statusCode / 100) === 4 || $statusCode === 200 || $this->errorCount >= 5) {
				throw $e;
			}
		} catch (Exception $e) {
			error_log($e);
			throw $e;
		}
		sleep($this->lpIdleTime * $this->errorCount);
	}
	protected abstract function handleEvent($events);
	protected abstract function handleChat($chat);
	protected abstract function handleFeed($feed);
}