<?php
class ChatGetMessage extends BaseCommand {

	/**
	 * 获得聊天信息
	 *
	 * @param $channel 频道ID  0 全部(包括公告)    -1(公告)   > 0 私聊频道
	 * @return array array('content' => array array('username'=>String ,''content'=> string,'gameuid'=>int,'time'=>int))
	 */
	
	protected function executeEx($params) {
		if (empty($params['channel']))
		{
			$this->throwException("No channel!", 211);
		}

		$channel = $params['channel'];

		require_once PATH_CACHE . 'ChatCache.php';
		$chatMC = new ChatCache($this->gameuid);
		$chatMC->setServer($this->server);

		//如果正在私聊，pull出自己的信息
		if ($channel > 0 ) {
			$list = $chatMC->pull($this->gameuid);
		}else{
			//否则pull出全部频道的信息
			$list = $chatMC->pull($channel);
		}

		//如果是公告
		if ($channel < 0) {
			foreach ($list as $key => $value) {
				if ($value['gameuid'] >0) {
					unset($list[$key]);
				}
			}
		}
		
		//如果全部频道没有聊天，开启自动聊天系统（- -！忽悠新手）
		if (count ( $list ) == 0 && in_array ( $this->gameuid % 10, array (5, 6) ) && $channel = 0) {
			$loginTime = $this->user_account ['createtime'];
			if (time () - $loginTime < 60 * 60 * 2)
			{
				$list = array_merge ( $list, $this->randMessage () );
			}
		}
		
		
		//如果开启了付费，他刷新全部频道的时候，那么就要看看他还剩多少免费的聊天
		if ($chatMC->isPay && $channel = 0)
		{
			$surplus = $chatMC->getSurplus();
			$list['surplus'] = $surplus;
		}else{
			$list['surplus'] = 1;
		}
		
		return $list;
	
	}
	
	private function randMessage()
	{
		$nameArr = array('有美','朱君','愤怒的小白','皇帝','荷花','正太','拖鞋','雄治','杏樹','美姫','香','趙子龍','三国');
		$messageArr =array('求加好友','谁有A卡','快来解救我','大家多少级了？','我有B级吕布~~~~','有没有人有A卡？','我15级啊','在哪能找到你？？','推图推失败了，怎么了？？？','大家这么活跃~~','游戏很有意思啊');
		return array( array ('content' =>$messageArr[ array_rand($messageArr)], 'username' => $nameArr[ array_rand($nameArr)], 'gameuid' => '1' ));
	}
}
?>