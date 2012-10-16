<?php
class ChatSendMessage extends BaseCommand {
	
	protected $cost = -10;            //每次扣除元宝的数量
	protected $maxLength=200;         //最多一百个汉字
	protected $logText = true;        //是否开启文本log
	
	/**
	 * 添加聊天信息
	 * @param $params['receiver'] int      接受者
	 * @param $params['content'] string    聊天信息
	 * @return array 聊天数据和免费余量
	 */
	
	protected function executeEx($params)
	{
		if(trim($params['content'])== ''){
			$this->throwException("Chat Content is NULL", 211);
		}
		if (empty($params['receiver']))
		{
			$this->throwException("No receiver!", 211);
		}	
		if(strlen($params['content']) > $this->maxLength)
		{
			$this->throwException("Chat content is too long", 211);
		}
		
		$receiver = $params['receiver'];
		$content=$this->sec($params['content']);
		
		$message = array(
					'gameuid' => $this->gameuid,
					'displayname' => $this->user_account['displayname'],
					'content' => $content
				);
		
// 		require_once PATH_CACHE . 'ChatCache.php';
// 		$receiverMC = new ChatCache($receiver);
        
		$receiver = $this->getInstance('ChatCache',$receiver,1);
		$receiver->
		
		//全部频道付费时
		if ($receiver < 0 && $receiverMC->isPay)
		{
			$chatMC=new ChatCache( $this->gameuid);
			$chatMC->setServer($this->server);
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
		
		$receiverMC->setServer($this->server);
		$receiverMC->push($message);
		
		if($this->logText)
		{
			$this->writeText($message);
		}
		$resault = array();
		$resault = $receiverMC->pull();
		$resault['surplus'] = $surplus;
		
		return $resault;
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