<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/AccountHandler.php';
include_once 'handler/BehaveHandler.php';
include_once 'handler/ArticleHandler.php';
include_once 'handler/MailHandler.php';
include_once 'handler/LocalHandler.php';
include_once 'handler/WordsHandler.php';
include_once 'handler/GuessHandler.php';
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
		$ret ['mailcount']=$mail->getUnreadCount();
		
		$game = new ArticleHandler ( $this->uid );
		$temgame=$game->getGameLast();
		
		$ret['newgame']=1;
		$ret['newgameurl']="http://tieba.baidu.com/f?ie=utf-8&kw=%E7%88%B1%E4%B8%8A%E8%81%9A%E4%BC%9A&fr=search";
		$ret['newgamecontent']=$temgame;
		
		//返回本地游戏列表
		$local = new LocalHandler ( $this->uid );
		$ret['local']=$local->getPage(0);
		
		$words = new WordsHandler ( $this->uid );
		$ret['wordskind']=$words->getTypeList();
		
		$guess = new GuessHandler ( $this->uid );
		$ret['guesskind']=$guess->getTypeList();
		
		
		$account->resetPushCount($account->gameuid,0);
		return $this->reutrnDate ( COMMAND_ENPTY ,$ret);
	}
}