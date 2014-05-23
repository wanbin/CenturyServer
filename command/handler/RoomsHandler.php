<?php
/**
 * 玩家房间基本类
 */
require_once PATH_CACHE . 'RoomsCache.php';
class RoomsHandler extends RoomsCache{
	public function NewRoom(){
		return parent::NewRoom();
	}
	
	/**
	 * 
	 * @param unknown_type $type 1,谁是卧底 2，杀人游戏
	 */
	public function StartGame($type){
		$roomInfo=$this->GetRoomInfo($this->gameuid);
		$userCount=count($roomInfo['room_user']);
		
		if ($type == 1) {
			if($userCount<4){
				// 			return false;
			}
			$this->setRoomType($type);
			include_once PATH_HANDLER . 'UnderCoverRoomHandler.php';
			$ucroom = new UnderCoverRoomHandler ( $this->uid );
			$roomContent = $ucroom->initcontent ( $userCount );
			
		}
	
		//准备发送推送
		include_once PATH_HANDLER . 'AccountHandler.php';
		$account = new AccountHandler ( $this->uid );
		foreach ($roomInfo['room_user'] as $key=>$value){
			$content="身份：".$roomContent['content'][$key];
			$this->setUserContent($value['gameuid'], $content);
			$account->sendPushByGameuid($value['gameuid'], $content);
		}
		exit();
		
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
			parent::distroyRoom ();
			$userInfo = $account->getAccountByUid ( $this->uid );
			$str = $userInfo ['username'] . "解散了房间" . $this->gameuid;
			$account->sendPushByTag ( "ROOM_" . $this->gameuid, $str );
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