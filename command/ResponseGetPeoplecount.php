<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/RankHandler.php';
class ResponseGetPeoplecount extends BaseCommand {
	protected function executeEx($params) {
		$gametype=$params['game'];
		$level=$params['level'];
		include_once PATH_HANDLER . 'RankHandler.php';
		$rank = new RankHandler ( $this->uid );
		$ret=$rank->getPeoplecount ($gametype,$level);
		return $this->returnDate ( COMMAND_SUCCESS,$ret);
	}
}