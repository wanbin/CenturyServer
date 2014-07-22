<?php
class ChatSendMessage extends BaseCommand {
	
	protected $cost = -10;            //每次扣除元宝的数量
	protected $maxLength=200;         //最多一百个汉字
	protected $logText = true;        //是否开启文本log
	
	/**
	 * 添加聊天信息
	 * @param $receiver 0 全部(包括公告) gameuid(与此人的私聊)
	 * @param $params['content'] string    聊天信息
	 * @return array()       上次刷新到现在新增的聊天记录   
	 */
	
	protected function executeEx($params)
	{
		//聊天内容不能为空
		if(trim($params['content'])== ''){
			$this->throwException("Chat Content is NULL", 211);
		}
		//接收者，默认为公共频道
		if (empty($params['receiver']))
		{
			$params['receiver'] = -1;
		}	
		//聊天内容字数限制
		if(strlen($params['content']) > $this->maxLength)
		{
			$this->throwException("Chat content is too long", 211);
		}
		//上一次刷新的时间
		if (empty($params['lastTime']))
		{
			$lastTime = $params['id'];
		}else{
			$lastTime = $params['lastTime'];
		}
		
		$receiver = $params['receiver'];
		$content=$this->sec($params['content']);
		$lang = $this->user_account['lang'];
		
		require_once PATH_CACHE . 'ChatCache.php';
		$chatMC = new ChatCache( $this->gameuid);
		$chatMC->setServer($this->server);
		$message = $chatMC->createMessage($this->gameuid, $this->user_account['displayname'], $content,$lang);

		//全部频道付费时
		if ($receiver < 0 && $chatMC->isPay)
		{
			$surplus = $chatMC->getSurplus();
			if ($surplus < 0){
				$points=array('points' => $this->cost);
				$this->checkUserStatus($points);
				$this->updateUserStatus($points);
			}else{
				$chatMC->setSurplus($surplus);
			}
		}else{
			$surplus = 1;
		}
		
		// push $message To $receiver
		$chatMC->push($message,$receiver);
		
		//如果是私聊,那么也需要给自己push一份
		if ($receiver > 0) {
			$chatMC->push($message,$this->gameuid);
		}
		if($this->logText)
		{
			$this->writeText($message);
		}
		
		$resault = array();
		//如果是私聊，则pull出自己的记录
		if ($receiver > 0){
			$resault['content'] = $chatMC->pull($this->gameuid,$lastTime,$lang);
		}else{
			//如果是全部频道，则pull出全部频道的内容
			$resault['content'] = $chatMC->pull($receiver,$lastTime,$lang);
		}
		
		$resault['surplus'] = $surplus;
		$resault['lastTime'] = time();
		$resault['cur_id'] = $resault['lastTime'];
		
		return array_values($resault);
	}


	protected function writeText($message)
	{
		$msg = implode("|", $message)."|".date("Y-m-d H:i:s")."\n";
		$log_dir = $GLOBALS['config']['log_path'] . 'chat_logs/';
		if(!file_exists($log_dir)){
			mkdir($log_dir,0777,true);
		}
		$log_file = sprintf('%schat_log_%s.log',$log_dir,date('Ymd'));
		file_put_contents($log_file, $msg,FILE_APPEND);
		return true;
	}
	
}
?>