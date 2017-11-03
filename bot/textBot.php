<?php
require_once BASE_ROOT.'/bot/baseBot.php';

class TextBot extends BaseBot {
	public function __construct() {
		parent::__construct();
	}
	protected function handleEvent($events) {
		foreach ($events as $event) {
			switch ($event->type) {
				case 'chat.message':
					$this->handleChat($event->chat);
					break;
				case 'feed.feed':
				case 'feed.reply':
					$this->handleFeed($event->feed);
					break;
			}
		}
	}
	protected function handleChat($chat) {
		if ($message->type === 1) {
			$message = $this->edge->getMessage($chat->room, $chat->msg);
			$room = $chat->room;
			// 장문 메시지 처리
			if ($message->len !== mb_strlen($message->content)) {
				$message = $this->edge->getLongMessage($chat->room, $chat->msg);
				$data['content'] = $message;
				$this->edge->createMessage($room, $data);
			} else {
				$data['content'] = $message->content;
				$this->edge->createMessage($room, $data);
			}
		}
	}
	protected function handleFeed($feed) {
		// Do something
	}
}