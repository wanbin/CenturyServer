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
			if(key_exists($value, $gameuid_name)){
				$temPublish=$punish->getRandomOne(1);
				$content="惩罚：".$temPublish[0]['content'];
				$this->setUserContent($value, $content);
				$result[]=array('username'=>$gameuid_name[$value],'content'=>$content,'gameuid'=>$value);
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
	public function StartGame($type){
		$this->setRoomType($type);
		$roomInfo=$this->GetRoomInfo($this->gameuid);
		$userCount=count($roomInfo['room_user']);
// 		$userCount=10;
		if ($type == 1) {
			if($userCount<4){
// 						return false;
			}
			$this->setRoomType($type);
			include_once PATH_HANDLER . 'UnderCoverRoomHandler.php';
			$ucroom = new UnderCoverRoomHandler ( $this->uid );
			$roomContent = $ucroom->initcontent ( $userCount );
		}
		else if($type==2){
			//杀人游戏分配身份
			include_once PATH_HANDLER . 'UnderCoverRoomHandler.php';
			$ucroom = new UnderCoverRoomHandler ( $this->uid );
			$roomContent=$ucroom->initKiller($userCount);
		}
		
		
	
		//准备发送推送
		include_once PATH_HANDLER . 'AccountHandler.php';
		$account = new AccountHandler ( $this->uid );
		foreach ($roomInfo['room_user'] as $key=>$value){
			$content="身份：".$roomContent['content'][$key];
			$this->setUserContent($value['gameuid'], $content);
			$roomInfo['room_user'][$key]['content']=$roomContent['content'][$key];
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
	
	public function GetRoomInfo($roomid){
			return parent::GetRoomInfo($roomid);
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