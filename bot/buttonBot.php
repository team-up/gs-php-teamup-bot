<?php
require_once BASE_ROOT.'/bot/baseBot.php';

class ButtonBot extends BaseBot {
	private $extraExample = array(
		array(
			'version' => 1,
			'type' => 'answer',
			'msg_buttons' => array(
				array(
					'text' => '팀업 API 문서',
					'type' => 'url',
					'url' => 'http://team-up.github.io'
				),
				array(
					'text' => '일반 텍스트',
					'type' => 'text'
				)
			),
			'input_buttons' => array(
				array(
					'text' => '버튼',
					'type' => 'text'
				),
				array(
					'text' => '안녕',
					'type' => 'text'
				)
			)
		)
	);
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
			if (!empty($message->extras) && $message->extras[0]->type === 'init') {
				$this->handleInitMessage($chat->room, $message);
				return ;
			}
			$room = $chat->room;
			// 장문 메시지 처리
			if ($message->len !== mb_strlen($message->content)) {
				$message->content = $this->edge->getLongMessage($room, $chat->msg);
			}
			if ($message->content === '버튼') {
				$data = array(
					'content' => '버튼',
					'extras' => $this->extraExample
				);
				$this->edge->createMessage($room, $data);
			}
		}
	}
	protected function handleFeed($feed) {
		// Do something
	}
	private function handleInitMessage($room, $message) {
		$version = $message->extras[0]->version;
		if ($version === 1) {
			$data = array(
				'content' => "안녕하세요! 팀업 PHP 샘플 봇입니다.\n'버튼'이라고 말하면 버튼을 보여줍니다.",
				'extras' => $this->extraExample
			);
			$this->edge->createMessage($room, $data);
		}
	}
}