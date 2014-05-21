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
	
	public function LevelRoom($roomid){
		if($roomid==$this->gameuid){
			$this->DelRoom();
		}
	}
	
	public function StartGame($type){
		
	}
	

	
	
}