<?php
// 查询需要审核的词汇
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class PublishAll extends BaseCommand {
	protected function executeEx($params) {
		$content = $params ['content'];
		$page = $params ['page'];
		//是否是需要审核的词汇
		$shenhe = $params ['shenhe'];
		$publish = new PunishHandler ( $this->uid );
		if ($shenhe == 1) {
			$ret = $publish->getPageShenHe ( $page );
		} else {
			$count = $publish->getTypeCount ( - 1 );
			$rand = rand ( 1, $count % PAGECOUNT );
			$ret = $publish->getPageList ( $rand, - 1 );
		}
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}

}