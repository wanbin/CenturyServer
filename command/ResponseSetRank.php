<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class ResponseSetRank extends BaseCommand {
	protected function executeEx($params) {
		$gametype=$params['game'];
		$level=$params['level'];
		$souce=$params['souce'];
		include_once PATH_HANDLER . 'RankHandler.php';
		$rank = new RankHandler ( $this->uid );
		$rankold=$rank->getRank($gametype, $level);
		$rank->setRank ($gametype,$level,$souce);
		$ret=$rank->getRank($gametype, $level);
		return $this->reutrnDate ( COMMAND_SUCCESS,array('rank'=>$ret,'rankold'=>$rankold));
	}
}