<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/RankHandler.php';
class ResponseChuangGuan extends BaseCommand {
	protected function executeEx($params) {
		$level=$params['level'];
		$name=$params['name'];
		include_once PATH_HANDLER . 'RankHandler.php';
		$rank = new RankHandler ( $this->uid );
		$ret=$rank->changguan ($level,$name);
		return $this->reutrnDate ( COMMAND_SUCCESS,array('ret'=>$ret));
	}
}