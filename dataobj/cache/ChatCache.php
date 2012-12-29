<?php
require_once PATH_MODEL . 'ChatModel.php';

class ChatCache extends ChatModel 
{
	private $max = 5;	 //队列长度
	private $limit = 3;  //每次返回长度
	private $free  = 5;  //免费条数
	private $list = array();
	public  $isPay = true ;

	
	//推入内容
	public function push($content)
	{
		$node = $this->createNode($content);
		//设置指针数据
		$this->setPointer();
		$key  = $this->getCacheKey('node');
		$this->setToCache($key, $node,3600,$this->gameuid);
	}
	
	
	//拉取列表
	public function pull()
	{
		$key  = $this->getCacheKey('node');
		$node = $this->getFromCache($key, $this->gameuid);
		$this->list[]=$node['data'];
		$count = 1;
		while($node['next'] && $count < $this->limit)
		{
			$node = $this->getFromCache($node['next'], $this->gameuid);
			echo $node['next'];
			if (!empty($node))
			{
				$this->list[] = $node['data'];
				$count++;
			}
			
		}
		
		return $this->list;
	}
	
	//生成结点
	protected function createNode($content)
	{
		$next = $this->getCacheKey('node');
		$node = array(
					'data' => $content,
					'next' => $next
				);
		return $node;
	}
	
	
	protected function getCacheKey($key)
	{
		switch($key)
		{
			case 'pointer':
				return sprintf(MEMCACHE_KEY_POINTER_SERVER_GAMEUID,$this->server,$this->gameuid);
				break;
			case 'surplus':
				return sprintf(MEMCACHE_KEY_SURPLUS_SERVER_GAMEUID,$this->server,$this->gameuid);
				break;
			case 'node':
				$position = $this->getPosition();
				if ($position === 0)
				{
					$position = $this->max;
				}
				return sprintf(MEMCACHE_KEY_NODE_SERVER_GAMEUID_POSITION,$this->server,$this->gameuid,$position);
				break;
			
			default:break;
		}
		
	}
	
	//获取指针位置
	protected function getPosition()
	{
		$key = $this->getCacheKey('pointer');
		$position = $this->getFromCache($key, $this->gameuid);
		
		//初始化情况
		if (empty($position))
		{
			$position = 0;
		}
		
		//队列满员情况
		if ($position > $this->max)
		{
			$position = $position % $this->max;
		}
		return $position;
	}
	
	
	//设置指针
	protected function setPointer()
	{
		$position = $this->getPosition();
		$key = $this->getCacheKey('pointer');
		
		$position ++;
		$this->setToCache($key, $position,3600, $this->gameuid);
	}
	
	
	//获取余量
	public function getSurplus()
	{
		$key = $this->getCacheKey('surplus');		
		$res = $this->getFromCache($key, $this->gameuid);

		if(empty($res))
		{
			$res = parent::getSurplus($this->gameuid);
			$this->setToCache($key, $res,3600, $this->gameuid);
		}
		
		if($res['chatdate'] != date('Ymd'))
		{
			$surplus = $this->free;
		}else{
			$surplus = $this->free - $res['surplus'];
		}
		return $surplus;
	}
	
	
	//设置免费余量
	public function setSurplus($surplus)
	{
		$key = $this->getCacheKey('surplus');
		$res['chatdate'] = date('Ymd');
		$res['surplus']  = $this->free-$surplus+1;
		if ($res['surplus'] == $this->free+1)
		{
			parent::updateChatCounter($this->gameuid);
		}
		$this->setToCache($key, $res,3600, $this->gameuid);
	}
	
	
}
?>