<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/AccountHandler.php';
include_once 'handler/BehaveHandler.php';
include_once 'handler/ArticleHandler.php';
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
// 		$mail->mailSend(-1, $mail->gameuid, "欢迎来到这里");
		$ret ['mailcount']=$mail->getUnreadCount();
		
		$game = new ArticleHandler ( $this->uid );
		$temgame=$game->getGameLast();
// 		print_R($temgame);
		
		$ret['newgame']=1;
		$ret['newgameurl']="http://tieba.baidu.com/f?ie=utf-8&kw=%E7%88%B1%E4%B8%8A%E8%81%9A%E4%BC%9A&fr=search";
		$ret['newgamecontent']=$temgame;
		
		
		$account->resetPushCount($account->gameuid,0);
		return $this->reutrnDate ( COMMAND_ENPTY ,$ret);
	}
}