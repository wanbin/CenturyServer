<?php
// 审核词汇，必须是
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class PublishShenHe extends BaseCommand {
	protected function executeEx($params) {
		$id = $params ['id'];
		$type = $params ['type'];
		$publish = new PunishHandler ( $this->uid );
		$ret = $publish->changeShow ( $id, $type );
		return $this->returnDate ( COMMAND_SUCCESS, $ret );
	}

}