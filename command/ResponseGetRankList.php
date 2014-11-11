<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class ResponseGetRankList extends BaseCommand {
	protected function executeEx($params) {
		$gametype=$params['game'];
		$level=$params['level'];
		include_once PATH_HANDLER . 'RankHandler.php';
		$rank = new RankHandler ( $this->uid );
		$ret=$rank->getRankList ($gametype,$level);
		return $this->reutrnDate ( COMMAND_SUCCESS,$ret);
	}
}