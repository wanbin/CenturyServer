<?php
// 这是线上游戏的列表
include_once 'BaseCommand.php';
include_once 'handler/ArticleHandler.php';
class GameList extends BaseCommand {
	protected function executeEx($params) {
		$page = isset($params ['page'])?$params ['page']:1;
		//是否是需要审核的词汇
		$game = new ArticleHandler ( $this->uid );
		$ret = $game->getGameList ( $page,1 );
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}

}