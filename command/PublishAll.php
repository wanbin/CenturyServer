<?php
// 查询需要审核的词汇
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class PublishAll extends BaseCommand {
	protected function executeEx($params) {
		$page = $params ['page'];
		$type=$params['type'];
		// 是否是需要审核的词汇
		$publish = new PunishHandler ( $this->uid );
		$ret = $publish->getPageList ( $page, - 1 );
		return $this->returnDate ( COMMAND_ENPTY, $ret );
	}

}