<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/AccountHandler.php';
include_once 'handler/BehaveHandler.php';
include_once 'handler/MailHandler.php';
class ResponseGetInfo extends BaseCommand {
	protected function executeEx($params) {
		$uid = $params ['uid'];
		if (empty ( $uid )) {
			$uid=$this->uid;
		}
		$account = new AccountHandler ( $this->uid );
		$ret= $account->getAccountByUid($uid);
		
		$mail = new MailHandler ( $this->uid );
		$retmail = $mail->getOneMail ();
		
		if (! empty ( $retmail )) {
			$ret ['mail'] = array (
					$retmail
			);
		}
		include_once PATH_HANDLER . 'RankHandler.php';
		$rank = new RankHandler ( $this->uid );
		
		$ret['cell']=$rank->getCellValue();
		$ret['rank']=$rank->getCellRank();
		$ret['mrname']="初入江湖";
		
		$ret['helpurl']="http://www.centurywar.cn/?p=30";
		
		$account->resetPushCount($account->gameuid,0);
		return $this->reutrnDate ( COMMAND_ENPTY ,$ret);
	}
}