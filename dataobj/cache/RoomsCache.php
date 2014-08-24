<?php
/**
 * @author WanBin @date 2014-05-21
 * 游戏房间
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'RoomsModel.php';
class RoomsCache extends RoomsModel{
	protected function NewRoom() {
		$this->exitAllGame();
		$roomid = parent::NewRoom ();
		$rediskey = $this->getRoomRedisUserKey ( $roomid );
		$this->addToRoom ( $roomid );
		return $roomid;
	}
	
	protected function exitAllGame(){
		$userRoomInfo=$this->getUserRoomInfo($this->gameuid);
		if(!empty($userRoomInfo)){
			$this->removeSomeOne($userRoomInfo['roomid'], $this->gameuid);
		}
	}
	
	protected function addToRoom($roomid){
		$this->exitAllGame();
		$rediskey = $this->getRoomRedisUserKey ( $roomid );
		$roomInfo = $this->getInfo ( $roomid );
		if ($this->getListLen ( $rediskey ) >= $roomInfo ['maxcount']) {
			// 人数已经多了，不能再加了
			return -1;
		} else {
			$this->pushList ( $rediskey, $this->gameuid );
			parent::addToRoom ( $roomid );
		}
		return $this->getListLen($rediskey);
	}
	
	
	protected function getInfo($roomid){
		$key=$this->getRoomInfoKey($roomid);
		$ret=$this->getFromCache($key);
		if(empty($ret)){
			$ret=parent::getInfo($roomid);
			if(!empty($ret)){
				$this->setToCache($key, $ret);
			}
		}
		return $ret;
	}
	
	protected function getRoomUserList($roomid){
		$key = $this->getRoomRedisUserKey ( $roomid );
		return $this->getListAll($key);
	}
	
	protected function removeSomeOne($roomid, $gameuid) {
		$key = $this->getRoomRedisUserKey ( $roomid );
		$this->removeList ( $key,$gameuid );
		return $this->removeUserRoomInfo($gameuid);
	}
	
	
	protected function removeUserRoomInfo($gameuid){
		$key = $this->getRoomUserInfoKey ( $gameuid );
		$this->delFromCache($key);
		return parent::removeUserRoomInfo ($gameuid );
	}
	
	
	protected function setRoomType($roomid,$type,$content=''){
		parent::setRoomType($roomid,$type,$content);
		$key=$this->getRoomInfoKey($roomid);
		return $this->delFromCache($key);
	}
	
	protected function LevelFromRoom($roomid) {
		parent::LevelFromRoom($roomid);
		$key = $this->getRoomRedisUserKey ( $roomid );
		return $this->removeList ($key, $this->gameuid );
	}
	
	// 销毁自己创建的房间
	protected function distroyRoom($roomid) {
		$userList=$this->getRoomUserList($roomid);

		foreach ($userList as $key=>$gameuid){
			$this->removeUserRoomInfo($gameuid);
		}
		
		//结束的时候把用户list存入到mongo中
		$this->endAndUpdateRoomUser($roomid, $userList);
		$key = $this->getRoomRedisUserKey ( $roomid );
		$this->delRedis($key);
		return $userList;
	}
	
	
	protected function getRoomUserInfo($gameuid) {
		$key = $this->getRoomUserInfoKey ( $gameuid );
		$ret = $this->getFromCache ( $key );
		if (empty ( $ret )) {
			$ret = parent::getRoomUserInfo ( $gameuid );
			if (! empty ( $ret )) {
				$this->setToCache ( $key, $ret );
			}
		}
		return $ret;
	}
	
	
	
	protected function setUserContent($gameuid,$content){
		parent::setUserContent($gameuid, $content);
		$key = $this->getRoomUserInfoKey ( $gameuid );
		return $this->delFromCache($key);
	}
	
	private function getRoomInfoKey($roomid) {
		return sprintf ( CACHE_KEY_ROOMINFO, $roomid );
	}
	
	private function getRoomUserInfoKey($gameuid) {
		return sprintf ( CACHE_KEY_ROOMUSERINFO, $gameuid );
	}
	
	
	private function getRoomRedisUserKey($roomid) {
		return sprintf ( REDIS_KEY_ROOMUSER, $roomid );
	}
}