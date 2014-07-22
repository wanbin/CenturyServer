<?php
// 修改用户昵称
include_once 'BaseCommand.php';
include_once 'handler/PublishHandler.php';
class NameChange extends BaseCommand {
	protected function executeEx($params) {
		$username = $params ['username'];
		if (empty ( $username )) {
			$this->throwException ( 'username is empty', 1101 );
		}
		if (! empty ( $username )) {
			include_once PATH_HANDLER . 'AccountHandler.php';
			$account = new AccountHandler ( $this->uid );
			$account->changeUserName ( $username );
		}
		return $this->reutrnDate ( COMMAND_SUCCESS );
	}
}