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
		$cillect = new CollectHandler ( $this->gameuid );
		$cillect->newCollect ( $id, $type );
		return $this->reutrnDate ( COMMAND_SUCCESS );
	}
}