<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class ResponseGetRank extends BaseCommand {
	protected function executeEx($params) {
		$gametype=$params['game'];
		$level=$params['level'];
		include_once PATH_HANDLER . 'RankHandler.php';
		$rank = new RankHandler ( $this->uid );
		$ret=$rank->getRank ($gametype,$level);
		return $this->returnDate ( COMMAND_SUCCESS,$ret);
	}
}