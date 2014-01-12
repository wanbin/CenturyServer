<?php
// 审核词汇，必须是
include_once 'BaseCommand.php';
include_once 'handler/PublishHandler.php';
class PublishShenHe extends BaseCommand {
	protected function executeEx($params) {
		$id = $params ['id'];
		$type = $params ['type'];
		$publish = new PublishHandler ( $this->uid );
		$ret = $publish->changeShow ( $id, $type );
		return $this->reutrnDate ( COMMAND_SUCCESS, $ret );
	}

}