<?php
// 这是线上游戏的列表
include_once 'BaseCommand.php';
include_once 'handler/ArticleHandler.php';
class GameCollect extends BaseCommand {
	protected function executeEx($params) {
		$gameid = $params ['gameid'];
		$type = $params ['type'];
		// 是否是需要审核的词汇
		$game = new ArticleHandler ( $this->uid );
		$ret = $game->gameCollect ( $gameid, $type );
		
		return $this->returnDate ( COMMAND_ENPTY, $ret );
	}
}