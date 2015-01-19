<?php
//所有用户喜欢的词汇
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class PublishUserLikeAll extends BaseCommand {
	protected function executeEx($params) {
		$publish = new PunishHandler ( $this->uid );
		$ret = $publish->getUserLikeList ();
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}
}