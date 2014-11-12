<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class ResponseBehave extends BaseCommand {
	protected function executeEx($params) {
		$gametype=$params['gametype'];
		$level=$params['level'];
		$data=$params['data'];
		include_once PATH_HANDLER . 'RankHandler.php';
		$rank = new RankHandler ( $this->uid );
		$cellold=$rank->getCellValue();
		$cellnew=$rank->updateCell($gametype, $level, $data);
		$ret=array('cell'=>$cellnew,'cellold'=>$cellold);
		return $this->reutrnDate ( COMMAND_SUCCESS,$ret);
	}
}