<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/PublishHandler.php';
class PublishNew extends BaseCommand {
	protected function executeEx($params) {
		$content = $params ['content'];
		$type = $params ['type'];
		if (empty ( $content )) {
			$this->throwException ( 'content is empty', 1101 );
		}
		$publish = new PublishHandler ( $this->uid );
		$publish->newPublish ( $content, $type );
		return $this->reutrnDate ( COMMAND_SUCCESS );
	}
}