<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/PublishHandler.php';
class PublishAll extends BaseCommand {
	protected function executeEx($params) {
		$content = $params ['content'];
		$page = $params ['page'];
		$publish = new PublishHandler ( $this->gameuid );
		$ret = $publish->getPage ( $page );
		return $this->reutrnDate ( COMMAND_SUCCESS, $ret );
	}

}