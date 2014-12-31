<?php
/**
 * @author WanBin @date 2013-08-03
 * 微信用户表
 */
require_once PATH_MODEL.'BaseModel.php';
class WXModel extends BaseModel {
	
	public function Log($content) {
		$insert = array (
				'gameuid' => $this->gameuid,
				'content' => $content 
		);
		return $this->insertMongo ( $insert, 'wx_log' );
	}
	

	
	
	public function changeName($name){
		$content=array('nickname'=>$name);
		$where=array("_id"=>intval($this->gameuid) );
		$this->updateMongo($content, $where, 'users');
		return true;
	}
	/**
	 * 返回信息类
	 *
	 * @param unknown_type $keyword
	 * @param unknown_type $uid
	 * @return string
	 */
	public function returncontent($keyword) {
		$this->Log ( $keyword );
		$helpStr = $this->getSampleHelpStr ();
		$type = $keyword ;
		if ($type == "帮助" || $type == "【帮助】" || $type == "help" || $type == "?"|| $type == "？") {
			return $this->getHelpStr () ;
		}
		if ($type == "规则" || $type == "【规则】" || $type == "rule" || $type == "*") {
			return $this->getRuleStr () ;
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
		
		$type = $this->changeKeyword ( $keyword );
		if ($type > 3 && $type <= 15) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ( $this->uid );
			$str = $UnderRoomCache->initRoom ( $type );
			return $str . $helpStr."\n回复2或3显示本局惩罚";
		} else if ($type == 1) {
			$str = "谁是卧底游戏创建成功，您为法官，请输入参与人数（不包括法官 4-15人）：";
			return $str.$helpStr;
		} else if ($type == 2) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ($this->uid);
			$str = $UnderRoomCache->getChengfa ( 2 );
			return $str.$helpStr;
		} else if ($type == 3) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ($this->uid);
			$str = $UnderRoomCache->getChengfa ( 3);
			return $str.$helpStr;
		}else if ($type >= 10000) {
			$gameuid = $UnderCache->gameuid;
			include_once PATH_HANDLER . "/RoomsHandler.php";
			$room = new RoomsHandler ( $this->uid );
			$roomid=$type-10000;
			$ret = $room->JoinRoom ( $roomid );
			if ($ret==1) {
				return "加入房间成功";
			} else if($ret==-2){
				return "未查到房间信息";
			}
			else if($ret==1){
				return "加入房间失败";
			}
			return $str.$helpStr;
		}  
		else if ($type >= 1000) {
			$gameuid = $UnderCache->gameuid;
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ($this->uid);
			$str = $UnderRoomCache->getInfo ( $type );
			return $str.$helpStr;
		} else {
			include_once PATH_HANDLER.'PageHandler.php';
			$page=new PageHandler($uid);
			$ret=$page->getPageFromKey('WX_'.$keyword);
			
			if (! empty ( $ret ['content'] )) {
				return $ret ['content'];
			}
			
			include_once PATH_HANDLER . "WXHandler.php";
			$UnderCoverCache = new WXHandler ( $this->uid );
			$msgCount = $UnderCoverCache->getMessageCount($keyword);
			if ($msgCount > 1) {
				$strtem = "[得意]你是本游戏中第【 $msgCount 】位用户发送这条信息了！这或许就是缘分吧，虽然小编一时半会回答不了你的问题，但相信您一定会在游戏中找到乐趣的~\n===============\n先发个游戏帮助，您先看着，看有需要的内容吗\n================\n";
			} else {
				$strtem = "[可怜]小编找遍了所有用户发来的信息，没有发和和你这条重复的，不知如何是好,又要挨骂了~~\n================\n先发个游戏帮助，您先看着，看有需要的内容吗？\n================\n";
			}
			return $strtem.$this->getHelpStr();
		}
	}
	public function changeKeyword($keyword) {
		if (intval ( $keyword ) > 0) {
			return intval ( $keyword );
		}
		switch ($keyword) {
			case "玩" :
			case "开始" :
			case "开局" :
				return 1;
			case "怎么玩" :
			case "帮忙" :
			case "帮助内容" :
			case "怎么开始" :
				return "help";
		}
	}
	protected function getHelpStr() {
		include_once PATH_HANDLER.'PageHandler.php';
		$page=new PageHandler($uid);
		$ret=$page->getPageFromKey('WX_HELP');
		return $ret['content'];
	}
	//返回制作团队
	protected function getEmail() {
		return "谁是卧底请您选择项目：\n 4-14.创建谁是卧底游戏: \n 输入 20 返回真心话大冒险：";
	}
	protected function getSampleHelpStr() {
		return "\n\n【帮助】帮助内容 \n【规则】游戏规则";
	}
	protected function getRuleStr() {
		include_once PATH_HANDLER.'PageHandler.php';
		$page=new PageHandler($uid);
		$ret=$page->getPageFromKey('WX_UNDERCOVER_RULE');
		return $ret['content'];
	}
	

}