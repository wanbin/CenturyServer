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
		$version=$params['version'];
		
		$channel = $params ['channel'];
		if (empty ( $uid )) {
			$uid=$this->uid;
		}
		
		$account = new AccountHandler ( $this->uid,$channel );
		$ret= $account->getAccountByUid($uid);
		$account->accountLogin($version);
		
		
		
		$mail = new MailHandler ( $this->uid );
		
		$issendmail=version_compare($version, '3.11');
		// 当用户版本小于3.10的时候，提示房间问题
		if ($issendmail < 0&&$ret['channel']=='ANDROID') {
			$mail->systemSendNotDobule($this->gameuid, "亲爱的用户，3.09及以下版本网络游戏可能无法正常使用，请更新最新版本，感谢您的支持！");
		}
		
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
		return $this->returnDate ( COMMAND_ENPTY ,$ret);
	}
}