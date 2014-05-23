<?php
/**
 * 玩家房间基本类
 */
require_once PATH_CACHE . 'RoomsCache.php';
class RoomsHandler extends RoomsCache{
	public function NewRoom(){
		return parent::NewRoom();
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
	
	public function DelRoom(){
		
	}
	

	public function StartGame($type){
		
	}
	

	
	
}