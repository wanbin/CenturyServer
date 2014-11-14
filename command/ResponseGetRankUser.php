<?php
// 新建一个房间
//返回排行的用户
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class ResponseGetRankUser extends BaseCommand {
	protected function executeEx($params) {
		$gametype=$params['gametype'];
		$level=$params['level'];
		include_once PATH_HANDLER . 'RankHandler.php';
		$rank = new RankHandler ( $this->uid );
		$ret=$rank->getRankUser($gametype, $level);
		return $this->reutrnDate ( COMMAND_SUCCESS,array('rank'=>$ret,'rankme'=>array('rank'=>0,'score'=>0)));
	}
}