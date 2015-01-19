<?php
// 这是线上游戏的列表
include_once 'BaseCommand.php';
include_once 'handler/ArticleHandler.php';
class GameCollect extends BaseCommand {
	protected function executeEx($params) {
		$gameid=$params['gameid'];
		$type=isset($params['type'])?$params['type']:1;
		// 是否是需要审核的词汇
		$game = new ArticleHandler ( $this->uid );
		if ($type == 1) {
			$ret = $game->like ( $gameid );
		} else {
			$ret = $game->dislike ( $gameid );
		}
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}
}