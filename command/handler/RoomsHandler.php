<?php
/**
 * 玩家房间基本类
 */
require_once PATH_CACHE . 'RoomsCache.php';
class RoomsHandler extends RoomsCache{
	public function NewRoom(){
		//网络房间基数
		return parent::NewRoom()+10000;
	}
	
	
	/* 
	 * 玩家加入某个房间
	 * @see RoomsModel::JoinRoom()
	 */
	public function JoinRoom($roomid){
		$ret=parent::addToRoom ( $roomid );
		if ($ret>0) {
			include_once PATH_HANDLER . 'AccountHandler.php';
			$account = new AccountHandler ( $this->uid );
			$userInfo = $account->getAccountByUid($this->uid);
			$content = $userInfo ['username'] . "已经加入到游戏中";
			$account->sendPushByGameuid ( $roomid, $content );
			return 1;
		}
		return $ret;
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
		
		include_once PATH_HANDLER . 'PunishHandler.php';
		$punish = new PunishHandler ( $this->uid );
		//先判断一下玩家是否都在房间里，如果不在房间里，则
		$result=array();
		foreach ($gameuidarr as $key=>$value){
			if(key_exists($value, $gameuid_name)||in_array($value, array(-1,-2,-3,-4,-5))){
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
		}
		foreach ($gameuid_name as $key=>$value){
			if(!in_array($key, $gameuidarr)){
				$content="游戏胜利【".$ret['content'].'】';
				$this->setUserContent($key, $content);
				//$account->sendPushByGameuid($key, $content);
			}
		}
		
		return $result;
	}
	
	public function delSomeOne($gameuid){
		$roomInfo = $this->getRoomUserInfo ( $this->gameuid );
		if (empty ( $roomInfo )) {
			return false;
		}
		$ret = parent::removeSomeOne ( $roomInfo ['roomid'], $gameuid );
		if ($ret) {
			include_once PATH_HANDLER . 'AccountHandler.php';
			$account = new AccountHandler ( $this->uid );
			$content = '您被管理员移出房间';
			$account->sendPushByGameuid ( $gameuid, $content );
		}
		return $ret;
	}
	/**
	 * 
	 * @param unknown_type $type 1,谁是卧底 2，杀人游戏
	 */
	public function StartGame($type,$addPeople){
		$roomInfo=$this->GetRoomInfo($addPeople);
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
		$this->setRoomType($roomInfo['_id'],$type,$roomcontent);
	
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
	
	
	public function GetRoomInfoOne(){
		$ret= parent::getRoomUserInfo($this->gameuid);
		if($ret['roomid']>0){
			$roomInfo=$this->getInfo($ret['roomid']);
			$ret['roomid']+=10000;
			$ret['roominfo']=$roomInfo;
		}
		return $ret;
	}
	
	public function LevelRoom(){
		$userRoomInfo=$this->getRoomUserInfo($this->gameuid);
		$roomid=$userRoomInfo['roomid'];
		if($roomid>0){
			$roomInfo=$this->getInfo($roomid);
			if($roomInfo['gameuid']==$this->gameuid){
				//代表是自己创建了这个
				$retuser=parent::distroyRoom ($roomid);
				include_once PATH_HANDLER . 'AccountHandler.php';
				$account = new AccountHandler ( $this->uid );
				$userInfo = $account->getAccountByUid ( $this->uid );
				$str = $userInfo ['username'] . "解散了房间" . $this->gameuid;
				foreach ( $retuser as $key => $value ) {
					$temgameuid = $value;
					$account->sendPushByGameuid ( $temgameuid, $str );
				}
			}
			else{
				$userInfo = $account->getAccountByUid ( $this->uid );
				$content = $userInfo ['username'] . "离开了房间";
				$account->sendPushByGameuid( $roomid, $content );
				return true;
			}
		}
		else{
			return -1;
		}
	} 
	
	
	
	/**
	 *	这个是主持人取得信息的方式
	 */
	public function GetRoomInfo($addPeople=0) {
		//先取下这个用户对应的roomid
		$userRoomInfo=$this->getRoomUserInfo($this->gameuid);
		$roomid=$userRoomInfo['roomid'];
		$ret = $this->getInfo ( $roomid );
		$roomUserList = $this->getRoomUserList ( $roomid );
		$retpeople=array();
		//添加两个多余的玩家
		for($i=1;$i<=$addPeople;$i++){
			$retpeople[]=array('username'=>"NO. $i",'gameuid'=>"-".$i);
		}
		include_once PATH_HANDLER . 'AccountHandler.php';
		$account = new AccountHandler ( $this->uid );
		foreach ($roomUserList as $key=>$value){
			$retpeople[]=$account->getAccountByGameuid($value);
		}
		$ret['room_user']=$retpeople;
		return $ret;
	} 
	
	
}