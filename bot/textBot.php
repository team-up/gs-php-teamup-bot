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
		$message = $this->edge->getMessage($chat->room, $chat->msg);
		// 메시지 전송에만 반응
		if ($message->type === 1) {
			$room = $chat->room;
			if ($message->content[0] === '@' && $message->len > 1) {
				$data['content'] = ltrim($message->content, '@');
				if (strlen($data['content']) > 0) {
					$this->edge->createMessage($room, $data);
				}
			} elseif($message->content === '?') {
				$data['content'] = '메시지 앞에 @를 붙여 전송하면 따라하는 봇';
				$this->edge->createMessage($room, $data);
			}
		}
	}
	protected function handleFeed($feed) {
		// Do something
	}
}