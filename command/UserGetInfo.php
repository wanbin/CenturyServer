<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/AccountHandler.php';
include_once 'handler/BehaveHandler.php';
include_once 'handler/MailHandler.php';
class UserGetInfo extends BaseCommand {
	protected function executeEx($params) {
		$uid = $params ['uid'];
		$channel = $params ['channel'];
		if (empty ( $uid )) {
			$uid=$this->uid;
		}
		
		$account = new AccountHandler ( $this->uid,$channel );
		$ret= $account->getAccountByUid($uid);
		$account->accountLogin();
		
		$mail = new MailHandler ( $this->uid );
// 		echo $this->uid;
// 		echo $mail->gameuid;
// 		$mail->mailSend(-1, $mail->gameuid, "欢迎来到这里");
		
		$retmail = $mail->getOneMail ();
		
		if (! empty ( $retmail )) {
			$ret ['mail'] = array (
					$retmail
			);
		}
		
		$ret['newgame']=1;
		$ret['newgamename']="爱上聚会吧";
		$ret['newgameimage']="http://cnd.centurywar.cn/gameimg/tieba.png";
		$ret['newgameurl']="http://tieba.baidu.com/f?ie=utf-8&kw=%E7%88%B1%E4%B8%8A%E8%81%9A%E4%BC%9A";
		
		$account->resetPushCount($account->gameuid,0);
		return $this->reutrnDate ( COMMAND_ENPTY ,$ret);
	}
}