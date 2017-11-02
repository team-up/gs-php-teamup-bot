<?php
if (!defined('BASE_ROOT')) {
    define('BASE_ROOT', dirname(__DIR__));
}
require_once BASE_ROOT."/rest/ev.php";
require_once BASE_ROOT."/rest/edge.php";

class Bot {
	private $ev;
	private $edge;
	private $lpWaitTime;
	private $lpIdleTime;
	private $errorCount = 0;
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
			self::longPoll();
		}
	}
	private function longPoll() {
		try {
			$data = $this->ev->getEvent($this->lpWaitTime);
			$this->errorCount = 0;
			if (empty($data) || empty($data->events)) {
				sleep($this->lpIdleTime);
				return ;
			}
			self::handleEvent($data->events);
			return ;
		} catch (ApiException $e) {
			error_log($e);
			$this->errorCount += 1;
			$statusCode = $e->getCode();
			switch ($statusCode) {
				case 400:
				case 401:
				case 403:
				case 404:
					throw $e;
				default:
					if ($this->errorCount >= 30) {
						throw $e;
					}
			}
		} catch (AuthException $e) {
			error_log($e);
			$this->errorCount += 1;
			$statusCode = $e->getCode();
			switch ($statusCode) {
				case 200: // Auth API에서 error JSON 반환 시
				case 400:
				case 401:
				case 403:
				case 404:
					throw $e;
			}
			if ($this->errorCount >= 5) {
				throw $e;
			}
		} catch (Exception $e) {
			error_log($e);
			throw $e;
		}
		sleep($this->lpIdleTime);
	}
	private function handleEvent($events) {
		foreach ($events as $event) {
			switch ($event->type) {
				case 'chat.message':
					self::handleChat($event->chat);
					break;
				case 'feed.feed':
				case 'feed.reply':
					self::handleFeed($event->feed);
					break;
			}
		}
	}
	private function handleChat($chat) {
		$message = $this->edge->getMessage($chat->room, $chat->msg);
		$room = $chat->room;
		// 장문 메시지 처리
		if ($message->len !== mb_strlen($message->content)) {
			$message = $this->edge->getLongMessage($chat->room, $chat->msg);
			$this->edge->createMessage($room, $message);
		} else {
			$this->edge->createMessage($room, $message->content);
		}
	}
	private function handleFeed($feed) {
		// Do something
	}
}