<?php
class ChatGetMessage extends BaseCommand {

	/**
	 * 添加聊天信息
	 *
	 * @param $params['start'] int     	 查询开始地址
	 * @return array array('lastid'=>int,'content' => array array('username'=>int ,''content'=> string,'gameuid'=>int))
	 */
	
	protected function executeEx($params) {
		if (empty($params['receiver']))
		{
			$this->throwException("No receiver!", 211);
		}
		$receiver = $params['receiver'];
		          
		require_once PATH_CACHE . 'ChatCache.php';
		
		$receiverMC = new ChatCache ( $receiver );
		$receiverMC->setServer($this->server);
		$resault = array();
		$resault=$receiverMC->pull();
		
		if (count ( $resault ) == 0 && in_array ( $this->gameuid % 10, array (5, 6 ) )) {
			$loginTime = $this->user_account ['createtime'];
			if (time () - $loginTime < 60 * 60 * 2)
			{
				$resault = array_merge ( $resault, $this->randMessage () );
			}
		}
		
		
		
		if ($receiverMC->isPay && $receiver < 0)
		{
			$chatMC = new ChatCache($this->gameuid);
			$chatMC->setServer($this->server);
			$surplus = $chatMC->getSurplus();
			$resault['surplus'] = $surplus;
		}else{
			$resault['surplus'] = 1;
		}
		
		
		return $resault;
	
	}
	
	function randMessage()
	{
		$nameArr = array('有美','朱君','愤怒的小白','皇帝','荷花','正太','拖鞋','雄治','杏樹','美姫','香','趙子龍','三国');
		$messageArr =array('求加好友','谁有A卡','快来解救我','大家多少级了？','我有B级吕布~~~~','有没有人有A卡？','我15级啊','在哪能找到你？？','推图推失败了，怎么了？？？','大家这么活跃~~','游戏很有意思啊');
		return array( array ('content' =>$messageArr[ array_rand($messageArr)], 'username' => $nameArr[ array_rand($nameArr)], 'gameuid' => '1' ));
	}
}
?>