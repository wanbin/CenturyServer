<?php
/**
 * 玩家房间基本类
 */
require_once PATH_CACHE . 'RoomsCache.php';
class RoomsHandler extends RoomsCache{
	public function NewRoom(){
		return parent::NewRoom();
	}
	
	public function PunishSomeOne($gameuidarr){
		$roomid=$this->gameuid;
		$ret=$this->GetRoomInfo($roomid);
		$gameuid_name=array();
		foreach ($ret['room_user'] as $key=>$value){
			$gameuid_name[$value['gameuid']]=$value['username'];
		}
		include_once PATH_HANDLER . 'AccountHandler.php';
		$account = new AccountHandler ( $this->uid );
		
		include_once PATH_HANDLER . 'PublishHandler.php';
		$punish = new PublishHandler ( $this->uid );
		//先判断一下玩家是否都在房间里，如果不在房间里，则
		$result=array();
		foreach ($gameuidarr as $key=>$value){
			if(key_exists($value, $gameuid_name)||in_array($value, array(-1,-2))){
				$temPublish=$punish->getRandomOne(1);
				$content="惩罚：".$temPublish[0]['content'];
				$username = $gameuid_name [$value];
				if ($value < 0) {
					$username = "NO." . abs ( $value );
				}
				$result [] = array (
						'username' => $username,
						'content' => $content,
						'gameuid' => $value 
				);
				
				if($value>0){
					$this->setUserContent($value, $content);
					$account->sendPushByGameuid($value, $content);
				}
			}
			else{
				$content="游戏胜利:".$ret['content'];
				$this->setUserContent($value, $content);
				$account->sendPushByGameuid($value, $content);
			}
		}
		return $result;
	}
	
	public function delSomeOne($gameuid){
		$ret= parent::delSomeOne($gameuid);
		if($ret){
			include_once PATH_HANDLER . 'AccountHandler.php';
			$account = new AccountHandler ( $this->uid );
			$content='您被管理员移出房间';
			$account->sendPushByGameuid($gameuid, $content);
		}
		return $ret;
	}
	/**
	 * 
	 * @param unknown_type $type 1,谁是卧底 2，杀人游戏
	 */
	public function StartGame($type,$addPeople){
		
		$roomInfo=$this->GetRoomInfo($this->gameuid,$addPeople);
		$userCount=count($roomInfo['room_user']);
// 		$userCount=10;
		$roomcontent='';
		if ($type == 1) {
			if($userCount<4){
// 						return false;
			}
			include_once PATH_HANDLER . 'UnderCoverRoomHandler.php';
			$ucroom = new UnderCoverRoomHandler ( $this->uid );
			$roomContent = $ucroom->initcontent ( $userCount );
			$roomcontent="平民：".$roomContent['father']." 卧底：".$roomContent['son'];
		}
		else if($type==2){
			//杀人游戏分配身份
			include_once PATH_HANDLER . 'UnderCoverRoomHandler.php';
			$ucroom = new UnderCoverRoomHandler ( $this->uid );
			$roomContent=$ucroom->initKiller($userCount);
			$roomcontent="警察：".$roomContent['police']."人 平民：".$roomContent['killer'].'人';
		}
		$this->setRoomType($type,$roomcontent);
	
		//准备发送推送
		include_once PATH_HANDLER . 'AccountHandler.php';
		$account = new AccountHandler ( $this->uid );
		foreach ($roomInfo['room_user'] as $key=>$value){
			$content="身份：".$roomContent['content'][$key];
			$roomInfo['room_user'][$key]['content']=$roomContent['content'][$key];
			if(!isset($value['gameuid'])||$value['gameuid']<0){
				continue;
			}
			$this->setUserContent($value['gameuid'], $content);
			$account->sendPushByGameuid($value['gameuid'], $content);
		}
		
		
		$roomInfo['room_contente']=$roomContent;
		$roomInfo['roomtype']=$type;
		return $roomInfo;
	}
	
	/**
	 * 某个玩家出局
	 * @param unknown_type $gameuid
	 */
	public function killSomeOne($gameuid){
		
	}
	
	public function GetRoomInfo($roomid,$addPeople=0){
			return parent::GetRoomInfo($roomid,$addPeople);
	}
	public function GetRoomInfoOne(){
		return parent::GetRoomInfoOne();
	}
	
	public function LevelRoom(){
		include_once PATH_HANDLER . 'AccountHandler.php';
		$account = new AccountHandler ( $this->uid );
		$roomid = parent::LevelRoom ();
		// 代表没有房间
		if ($roomid == - 1) {
			return false;
		}		// 代表是自己创建的房间
		else if ($roomid == - 2) {
			$retuser=parent::distroyRoom ();
			$userInfo = $account->getAccountByUid ( $this->uid );
			$str = $userInfo ['username'] . "解散了房间" . $this->gameuid;
			foreach ( $retuser as $key => $value ) {
				$temgameuid = $value ['gameuid'];
				$account->sendPushByGameuid ( $temgameuid, $str );
			}
			return true;
		} else {
			$userInfo = $account->getAccountByUid ( $this->uid );
			$content = $userInfo ['username'] . "离开了房间";
			$account->sendPushByGameuid( $roomid, $content );
			return true;
		}
	} 
	
	
	
	public function JoinRoom($roomid){
		if (parent::JoinRoom ( $roomid )) {
			include_once PATH_HANDLER . 'AccountHandler.php';
			$account = new AccountHandler ( $this->uid );
			$userInfo = $account->getAccountByUid($this->uid);
			$content = $userInfo ['username'] . "已经加入到游戏中";
 			$account->sendPushByGameuid ( $roomid, $content );
			return true;
		}
		return false;
	}

	
	
}