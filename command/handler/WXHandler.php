<?php
require_once PATH_CACHE . 'WXCache.php';
class WXHandler extends WXCache{
	
	/**
	 * 返回信息类
	 *
	 * @param unknown_type $keyword
	 * @param unknown_type $uid
	 * @return string
	 */
	public function returncontent($keyword) {
		$this->Log ( $keyword );
		if (intval ( $keyword ) > 0) {
			$keyword = intval ( $keyword );
		}

		$temarr = explode ( ' ', trim ( $keyword ) );
		if (in_array($temarr [0], array("名字","姓名","昵称"))) {
			include_once PATH_HANDLER . "/AccountHandler.php";
			$account = new AccountHandler ( $this->uid );
			$str = $account->changeUserName ( $temarr[1] );
			return "修改名字成功，".$temarr[1];
		}
	
		if(in_array(trim($keyword), array("身份","刷新","r"))){
			include_once PATH_HANDLER . 'RoomsHandler.php';
			$room = new RoomsHandler ( $this->uid );
			$ret=$room->GetRoomInfoOne ();
			return $ret['content'];
		}
	
		if ($keyword > 3 && $keyword <= 15) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ( $this->uid );
			$str = $UnderRoomCache->initRoom ( $keyword );
			return $str ."\n回复2或3显示本局惩罚";
		} else if ($keyword == 2) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ($this->uid);
			$str = $UnderRoomCache->getChengfa ( 2 );
			return $str;
		} else if ($keyword == 3) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ($this->uid);
			$str = $UnderRoomCache->getChengfa ( 3);
			return $str;
		}else if ($keyword >= 10000) {
			$gameuid = $UnderCache->gameuid;
			include_once PATH_HANDLER . "/RoomsHandler.php";
			$room = new RoomsHandler ( $this->uid );
			$roomid=$keyword-10000;
			$ret = $room->JoinRoom ( $roomid );
			if ($ret==1) {
				return "加入房间成功";
			} else if($ret==-2){
				return "未查到房间信息";
			}
			else if($ret==1){
				return "加入房间失败";
			}
			return $str;
		}
		else if ($keyword >= 1000) {
			$gameuid = $UnderCache->gameuid;
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ($this->uid);
			$str = $UnderRoomCache->getInfo ( $keyword );
			return $str;
		} else {
			return $this->getReturn($keyword);
		}
	}

	public function getReturn($keyword){
		return parent::getReturn($keyword);
	}
	
	public function getReturnFromMongo($keyword){
		return parent::getReturnFromMongo($keyword);
	}
	public function delReturn($keyword) {
		return parent::delReturn ( $keyword );
	}
	public function updateReturn($keyword,$content) {
		return parent::updateReturn ( $keyword,$content);
	}
	public function newReturn($keyword, $content) {
		return parent::newReturn ( $keyword, $content );
	}
	public function getReturnList(){
		return parent::getReturnList();
	}
	public function getMessageList(){
		return parent::getMessageList();
	}
	public function delKey($key){
		return parent::delKey($key);
	}
	
}