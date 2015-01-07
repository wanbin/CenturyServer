<?php
// 这是线上游戏的列表
include_once 'BaseCommand.php';
include_once 'handler/ArticleHandler.php';
class GameOne extends BaseCommand {
	protected function executeEx($params) {
		$id = $params['gameid'];
		$helpName=isset($params['gamename'])?$params['gamename']:"";
		//是否是需要审核的词汇
		$game = new ArticleHandler ( $this->uid );
		if (! empty ( $helpName )) {
			$id = $game->getIdFromName ( $helpName );
		}
		$ret = $game->getOne($id);
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}

}