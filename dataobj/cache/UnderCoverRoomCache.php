<?php
/**
 * @author WanBin @date 2013-08-03
 * 谁是卧底房间信息
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'UnderCoverRoomModel.php';
class UnderCoverRoomCache extends UnderCoverRoomModel{
	public function initRoom($peoplecount) {
		//1000-9999为微信房间号
		$rediskey='room_redis';
		$roomall=$this->getRedisHashAll($rediskey);
		//这块如果真大于9999，那就犀利啦，同时有9000人在玩谁是卧底
		$roomid=0;
		foreach ($roomall as $temroomid=>$createtime){
			if($createtime<time()-3600){
				$roomid=$temroomid;
				break;
			}
		}
		if ($roomid == 0) {
			$roomid = count ( $roomall ) + 1;
		}
		$this->setRedisHash($rediskey, $roomid, time());
		$time = time ();
		
		$temContent = $this->initcontent ( $peoplecount );
		
		$roominfo=array(
				'roomid'=>$roomid,
				'gameuid'=>$this->gameuid,
				'peoplecount'=>$peoplecount,
				'conjson'=>$temContent ['content'],
				);
		$roomsaveid=$this->insertMongo($roominfo, 'room_save');
		
		
		//把房间信息放到memcache中
		$this->setToCache ( $this->getRoomKeyCache ( $roomid ), $roominfo );
		//清空当前用户redis
		$keyredisuser=$this->getRoomUserKeyRedis($roomid);
		$this->delRedis($keyredisuser);
		
		$father = $temContent ['father'];
		$son = $temContent ['son'];
		$sonIndex = $temContent ['sonindex'];
		$roomidAdd1000=$roomid+1000;
		$str = "创建房间 【 $roomidAdd1000 】 成功 \n 本房间人数：$peoplecount \n 平民：$father \n 卧底：$son \n 卧底编号： $sonIndex";
		if ($this->echoit) {
			echo $str;
		}
		
		return $str;
	}
	
	protected function getRoomInfo($roomid){
		$roomkey=$this->getRoomKeyCache ( $roomid );
		$roominfo=$this->getFromCache($roomkey);
		if(empty($roominfo)){
			$redissavekey='room_save_redis';
			$roomNewId=$this->getRedisHash($redissavekey, $roomid);
			if($roomNewId>0){
				
			}
		}
		return $roominfo;
	}
	
	
	public function getInfo($roomid) {
		$keyredisuser=$this->getRoomUserKeyRedis($roomid);
		
		$ret = $this->getRoomInfo ( $roomid - 1000 );
		if ($ret ['gameuid'] == $this->gameuid) {
			$nowcount = $this->getListLen($keyredisuser);
			$pcount = $ret ['peoplecount'];
			$str = "您创建了本房间：\n 当前人数:$nowcount \n总人数:$pcount \n";
			if ($this->echoit) {
				echo $str;
			}
			return $str;
		}
		
		
		$userIndex = $this->getUserRoomIndex ( $roomid, $this->gameuid );
		if ($userIndex > 0) {
			$str = $ret ['conjson'] [$userIndex - 1];
			$str = "您的身份为：$str\n您的编号为：$userIndex";
			if ($this->echoit) {
				echo $str;
			}
			return $str;
		}

		
		if ($ret ['peoplecount'] <= $this->getListLen($keyredisuser)) {
			$str = "此房间已满或已经超时，召集自己的好友回复1重新开一局吧~~";
			if ($this->echoit) {
				echo $str;
			}
			return $str;
		}

		//如果不在里面，把用户加入
		$this->pushList($keyredisuser, $this->gameuid);
		$userIndex=$this->getUserRoomIndex($roomid, $this->gameuid);
		$strshenfen = $ret ['conjson'] [$userIndex - 1];
		$str = "您的身份为：$strshenfen\n您的编号为：$userIndex";
		if ($this->echoit) {
			echo $str;
		}
		return $str;
	}
	
	private function getUserRoomIndex($roomid,$gameuid){
		$keyredisuser=$this->getRoomUserKeyRedis($roomid);
		$userArr=$this->getListAll($keyredisuser);
		foreach ( $userArr as $key => $value ) {
			if ($value ==$gameuid) {
				return $key+1;
			}
		}
		return 0;
	}
	
	private function getRoomKeyCache($roomid){
		return "room_cache_".$roomid;
	}
	private function getRoomUserKeyRedis($roomid){
		return "room_user_redis_".$roomid;
	}
}