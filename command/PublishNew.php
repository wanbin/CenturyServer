<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class PublishNew extends BaseCommand {
	protected function executeEx($params) {
		$content = $params ['content'];
		$type = isset($params ['type'])?$params ['type']:1;
		$username=$params['username'];
		if (empty ( $content )) {
			$this->throwException ( 'content is empty', 1101 );
		}
		if(!empty($username))
		{
			include_once PATH_HANDLER.'AccountHandler.php';
			$account = new AccountHandler ( $this->uid );
			$account->changeUserName($username);
		}
		$publish = new PunishHandler ( $this->uid );
		$publish->newPublish ( $content, $type );
		return $this->returnDate ( COMMAND_SUCCESS );
	}
}