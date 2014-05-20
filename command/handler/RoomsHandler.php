<?php
/**
 * 玩家房间基本类
 */
require_once PATH_CACHE . 'RoomsCache.php';
class RoomsHandler extends RoomsCache{
	public function NewRoom(){
		return parent::NewRoom();
	}
	
	public function JoinRoom($roomid){
		return parent::JoinRoom($roomid);
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