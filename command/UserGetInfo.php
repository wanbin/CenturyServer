<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/AccountHandler.php';
class UserGetInfo extends BaseCommand {
	protected function executeEx($params) {
		$uid = $params ['uid'];
		if (empty ( $uid )) {
			$uid=$this->uid;
		}
		$account = new AccountHandler ( $this->uid );
		$ret= $account->getAccountByUid($uid);
		return $this->reutrnDate ( COMMAND_ENPTY ,$ret);
	}
}