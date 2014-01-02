<?php
// 收藏，喜欢一个真心话
include_once 'BaseCommand.php';
include_once 'handler/CollectHandler.php';
class PublishCollect extends BaseCommand {
	protected function executeEx($params) {
		$id = $params ['id'];
		$type = $params ['type'];
		if (empty ( $id ) || empty ( $type )) {
			$this->throwException ( 'id or type is empty', 1101 );
		}
		$cillect = new CollectHandler ( $this->uid );
		if ($cillect->newCollect ( $id, $type )) {
			$this->reutrnDate ( COMMAND_SUCCESS );
		} else {
			$this->reutrnDate ( COMMAND_FAILE );
		}
	}
}