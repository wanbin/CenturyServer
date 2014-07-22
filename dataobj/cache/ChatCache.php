<?php
require_once PATH_DATAOBJ . 'ChatModel.php';

/**
 +----------------------------------------------------------
 *    ChatCache
 +----------------------------------------------------------
 *   channel ID
 *  -1 代表全部(包括公告)    0 代表公告       >0 为私聊
 +----------------------------------------------------------
 *  @author     Administrator
 *  @version    2012-12-31
 *  @package    package_name
 +----------------------------------------------------------
 */
class ChatCache extends ChatModel 
{
	private $max = 100;	 //队列长度
	private $limit = 10;  //每次返回长度
	private $free  = 5;  //免费条数
	private $list = array();
	public  $isPay = false ;     //是否开启付费
	
	private $langSwitch = True;
	
	private $cacheMC = NULL;
	private $channel  = NULL;
	
	public function __construct($gameuid)
	{
		parent::__construct($gameuid);
		if ( empty($this->cacheMC) ) {
			$this->cacheMC = parent::getCacheInstance('cache_chat');
		}
		
	}
	
	public function createMessage($gameuid,$name,$content,$lang)
	{
		$message = array(
					'gameuid' => $gameuid,
					'username' => $name,
					'content' => $content,
					'time'    => time(),
					'lang'   => $lang
				);
		return $message;
	}
	
	/**
	 +----------------------------------------------------------
	 * 生成公告
	 +----------------------------------------------------------
	 * @param string $displayname  得到卡牌的玩家姓名
	 * @param int $heroid          卡牌英雄ID
	 * @param int $countryid       英雄国家ID
	 +----------------------------------------------------------
	 */
	public function createNotice($displayname,$heroid,$city,$type='')
	{
		$notice = $this->createMessage( 0 , Notice, '','all');
		$data = array('city' => $city, 'hid' => $heroid, 'user' => $displayname ,'type'=>$type);
		$notice['data'] = $data;
		return $notice;
	}
	
	/*
	* push ($message,$receiver)  把$message 发给 $receiver
	*/
	public function push($message,$receiver)
	{
		$this->channel = $receiver;
		$cacheKey = $this->getCacheKey('node');
		$list = $this->cacheMC->get($cacheKey);
		
		$length = count($list);
		while ($length >= $this->max)
		{
			array_shift($list);
			$length--;
		}
		$list[] = $message;

		$this->cacheMC->set($cacheKey,$list,3600,$this->gameuid);
		
		// return $message;
		
	}
	

	/*
	*  pull($userid)  获取$userid 的消息
	*/
	public function pull($userid,$lastTime,$lang)
	{
		$this->channel = $userid;
		$cacheKey = $this->getCacheKey('node');
		$list = $this->cacheMC->get($cacheKey);

		$resault = array();
		if (!empty($list))
		{
			$i = 0;
			if($lastTime == 0)
			{
				$i = -$this->limit;
			}else{
				end($list);
				while($current = current($list))
				{
					if ($current['time'] <= $lastTime)
						break;
					$i--;
					prev($list);
				}
			}
			if ($i != 0)
				$resault = array_splice($list, $i);
		}
		
		if ($this->langSwitch)
		{
			foreach ($resault as $key=>$value)
			{
				if ($value['lang']!=$lang && $value['lang']!='all')
				{
					unset($resault[$key]);
				}
			}
		}
		
		return $resault;
	}
	
	protected function getCacheKey($key)
	{
		switch($key)
		{
			case 'surplus':
				return sprintf(MEMCACHE_KEY_SURPLUS_SERVER_GAMEUID,$this->server,$this->gameuid);
				break;
			case 'node':
				return sprintf(MEMCACHE_KEY_NODE_SERVER_GAMEUID,$this->server,$this->channel);
				break;
			case 'time':
				return sprintf(MEMCACHE_KEY_LASTTIME_SERVER_GAMEUID,$this->server,$this->channel,$this->gameuid);
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
		$this->cacheMC->set($cacheKey,time(),3600,$this->gameuid);
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
			$this->cacheMC->set( $key, $res, 3600, $this->gameuid );
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
		$this->cacheMC->set ( $key, $res, 3600, $this->gameuid );
	}	
	

}
?>