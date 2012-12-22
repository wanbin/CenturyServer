<?php
require_once PATH_DATAOBJ . 'ChatModel.php';

class ChatCache extends ChatModel 
{
	private $max = 5;	 //队列长度
	private $limit = 3;  //每次返回长度
	private $free  = 5;  //免费条数
	private $list = array();
	public  $isPay = true ;     //是否开启付费
	
	private $cacheMC = NULL;
	private $userid  = NULL;
	
	public function __construct($gameuid)
	{
		parent::__construct($gameuid);
		if ( empty($this->cacheMC) ) {
			$this->cacheMC = parent::getCacheInstance('cache_chat');
		}
		
	}
	
	public function createMessage($gameuid,$name,$content)
	{
		$message = array(
					'gameuid' => $gameuid,
					'displayname' => $name,
					'content' => $content,
					'time'    => time()
				);
		return $message;
	}
	
	/*
	* push ($message,$receiver)  把$message 发给 $receiver
	*/
	public function push($message,$receiver)
	{
		$this->userid = $receiver;
		$cacheKey = $this->getCacheKey('node');
		$list = $this->cacheMC->get($cacheKey);
		
		$length = count($list);
		while ($length >= $this->max)
		{
			array_shift($list);
			$length--;
		}
		$list[] = $message;

		$this->cacheMC->set($cacheKey,$list);
		
		// return $message;
		
	}
	

	/*
	*  pull($userid)  获取$userid 的消息
	*/
	public function pull($userid)
	{
		$this->userid = $userid;
		$cacheKey = $this->getCacheKey('node');
		$list = $this->cacheMC->get($cacheKey);

		$lastTime = $this->getLastTime();

		foreach ($list as $key => $value) {
			if ($value['time']<$lastTime) {
				unset($list[$key]);
			}
		}
		return $list;
	}
	
	protected function getCacheKey($key)
	{
		switch($key)
		{
			case 'surplus':
				return sprintf(MEMCACHE_KEY_SURPLUS_SERVER_GAMEUID,$this->server,$this->gameuid);
				break;
			case 'node':
				return sprintf(MEMCACHE_KEY_NODE_SERVER_GAMEUID,$this->server,$this->userid);
				break;
			case 'time':
				return sprintf(MEMCACHE_KEY_LASTTIME_SERVER_GAMEUID,$this->server,$this->gameuid);
			default:break;
		}
		
	}

	//获取玩家上次刷新时候的时间
	private function getLastTime()
	{
		$cacheKey = $this->getCacheKey('time');
		$lastTime = $this->cacheMC->get($cacheKey);
		if (empty($lastTime)) {
			$lastTime = 0 ;
		}
		$this->cacheMC->setToCache($cacheKey,time(),3600,$this->gameuid);
		return $lastTime;
	}
		
	// 获取余量
	public function getSurplus() 
	{
		$key = $this->getCacheKey ( 'surplus' );
		$res = $this->cacheMC->get( $key );
		
		if (empty ( $res )) 
		{
			$res = parent::getSurplus ( $this->gameuid );
			$this->cacheMC->setToCache ( $key, $res, 3600, $this->gameuid );
		}
		
		if ($res ['chatdate'] != date ( 'Ymd' )) 
		{
			$surplus = $this->free;
		} else {
			$surplus = $this->free - $res ['surplus'];
		}
		return $surplus;
	}
	
	// 设置免费余量
	public function setSurplus($surplus) 
	{
		$key = $this->getCacheKey ( 'surplus' );
		$res ['chatdate'] = date ( 'Ymd' );
		$res ['surplus'] = $this->free - $surplus + 1;
		if ($res ['surplus'] == $this->free + 1)
		 {
			parent::updateChatCounter ( $this->gameuid );
		}
		$this->cacheMC->setToCache ( $key, $res, 3600, $this->gameuid );
	}	
	

}
?>