<?php
/**
 * @author WanBin @date 2014-05-21
 * 游戏房间
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'RoomsModel.php';
class RankCache extends RoomsModel{
	
	protected function changguan($level,$name) {
		$key = $this->getChuangGuanKey ( $level );
		if (! $this->isExit ( $key, $this->gameuid )) {
			if ($this->getListLen ( $key ) == 0) {
				// 这个是全新开启的一阶游戏，触发事件
				file_put_contents ( "newlevel$level.log", $this->gameuid, FILE_APPEND );
			}
			$this->pushList ( $key, $this->gameuid );
			$keycount = $this->getChuangGuanPeople ();
			$this->incrList ( $keycount, $level );
			return true;
		}
		return false;
	}
	
	
	protected function getchuanguancount($level) {
		$keycount = $this->getChuangGuanPeople ();
		return $this->getRedisHashAll($keycount);
	}
	
	
	protected function getRank($gametype,$level) {
		$key=$this->getGameRankKey($gametype,$level);
		//如果是时间的，按越少越胜利
		if($gametype==102){
			$rank=$this->getSortRankLowToHigh($key, $this->gameuid);
		}else{
			$rank=$this->getSortRank($key, $this->gameuid);
		}
		if($rank===false){
			return 0;
		}
		return $rank+1;	
	}
	
	public function setRank($gametype,$level,$souce){
		$key=$this->getGameRankKey($gametype,$level);
		return $this->sortAdd($key,$souce,$this->gameuid);
	}
	
	private function getGameRankKey($gametype,$level) {
		return sprintf ( REDIS_GAME_RANK,$gametype, $level );
	}
	
	
	private function getChuangGuanKey($level) {
		return sprintf ( REDIS_CHUANG_GUAN, $level );
	}
	
	private function getChuangGuanPeople() {
		return sprintf ( REDIS_CHUANG_GUAN_COUNT);
	}
	
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
		if(empty($roomInfo)){
			return -2;
		}
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
	
	
}