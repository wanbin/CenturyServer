<?php
// 这是线上游戏的列表
include_once 'BaseCommand.php';
include_once 'handler/ArticleHandler.php';
class GameOne extends BaseCommand {
	protected function executeEx($params) {
		$id = $params['gameid'];
		// 是否是需要审核的词汇
		$game = new ArticleHandler ( $this->uid );
		if (intval ( $id ) == 0) {
			$id = $game->getIdFromName ( $id );
		}
		$ret = $game->getOne($id);
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}

}