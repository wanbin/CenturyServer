<?php
// 随机返回一条词汇
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class PublishRandomOne extends BaseCommand {
	protected function executeEx($params) {
		// 是否是需要审核的词汇
		$publish = new PunishHandler ( $this->uid );
		$getrandom=$publish->getRandomOne ( -1 );
		$ret =array('content'=>array($getrandom)) ;
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}
}