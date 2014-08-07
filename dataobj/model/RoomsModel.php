<?php
/**
 * @author WanBin @date 2014-05-21
 * 游戏房间
 */
require_once PATH_MODEL.'BaseModel.php';
class RoomsModel extends BaseModel {
	
	protected  function NewRoom(){
		$gameuid = $this->gameuid;
		$maxcount = 10;
		$sql = "delete from room where gameuid=$gameuid;insert into room(gameuid,maxcount,nowcount,createtime)values($gameuid,$maxcount,1,now());";
		$sql2 = "delete from user_rooms where gameuid=$gameuid;insert into user_rooms(gameuid,roomid,createtime)values($gameuid,$gameuid,now());";
		$this->oneSql ( $sql.$sql2  );
		return true;
	}
	

	//离开房间
	protected function LevelRoom() {
		$gameuid = $this->gameuid;
		$sql = "select * from user_rooms where gameuid=$gameuid";
		$ret = $this->oneSqlSignle ( $sql );
		$roomid = $ret ['roomid'];
		if (empty ( $roomid )) {
			return - 1;
		}
		if($roomid==$gameuid){
			return -2;
		}
		$sql = "update room set nowcount=nowcount-1 where gameuid=$roomid;";
		$sql2 = "delete from user_rooms where gameuid=$gameuid";
		$this->oneSql ( $sql . $sql2 );
		return $roomid;
	}
		
		// 销毁自己创建的房间
	protected function distroyRoom() {
		$gameuid = $this->gameuid;
		$sql = "select gameuid from user_rooms where roomid=$gameuid and gameuid!=$gameuid;";
		$ret = $this->oneSql ( $sql );
		$sql = "delete from user_rooms where roomid=$gameuid;";
		$sql2 = "delete from room where gameuid=$gameuid;";
		$this->oneSql ( $sql . $sql2 );
		return $ret;
	}
	
	protected function JoinRoom($id){
		$gameuid = $this->gameuid;
		$sql="select * from room where gameuid=$id";
		$ret=$this->oneSqlSignle($sql);
		if(empty($ret)||$ret['maxcount']<=$ret['newcount']){
			return false;
		}

		$sql="update room set nowcount=nowcount+1 where gameuid=$id;";
		$sql2 ="delete from user_rooms where gameuid=$gameuid;insert into user_rooms(gameuid,roomid,content,createtime)values($gameuid,$id,'已经加入游戏，还未开始',now());";
		$this->oneSql ( $sql);
		$this->oneSql($sql2);
		return true;
	}
	
	protected function GetRoomInfo($roomid,$addPeople=0) {
		$sql = "select * from room where gameuid=$roomid";
		$ret = $this->oneSqlSignle ( $sql );
		$sql2 = "select user_rooms.*,username from user_rooms,wx_account where wx_account.gameuid=user_rooms.gameuid and  roomid=$roomid order by createtime";
		$ret2 = $this->oneSql ( $sql2 );
		$retpeople=array();
		//添加两个多余的玩家
		for($i=1;$i<=$addPeople;$i++){
			$retpeople[]=array('username'=>"NO. $i",'gameuid'=>"-".$i);
		}
		
		foreach ($ret2 as $key=>$value){
			$retpeople[]=$value;
		}
		
		$ret ['room_user'] = $retpeople;
		return $ret;
	}
	protected function setUserContent($gameuid,$content){
		$udpatetime=time();
		$sql = "update user_rooms set content='$content',updatetime=now() where gameuid=$gameuid";
		$this->oneSql($sql);
		return true;
	}
	
	protected  function delSomeOne($gameuid){
		$roomid=$this->gameuid;
		$sql="update room set nowcount=nowcount-1 where gameuid=$roomid;";
		$sql2 ="delete from  user_rooms where gameuid=$gameuid";
		$this->oneSql ( $sql . $sql2 );
// 		echo  $sql . $sql2 ;
		return true;
	}
	
	protected function setRoomType($type){
		$udpatetime=time();
		$gameuid=$this->gameuid;
		if ($type == 1) {
			$name = "谁是卧底";
		} else if ($type == 2) {
			$name = "杀人游戏";
		}
		$sql = "update room set type=$type,name='$name',updatetime=now() where gameuid=$gameuid";
		$this->oneSql($sql);
		return true;
	}
	
	protected function GetRoomInfoOne() {
		$roomid=$this->gameuid;
		$sql = "select user_rooms.*,name gamename from user_rooms,room where room.gameuid=user_rooms.roomid and user_rooms.gameuid=$roomid";
		$ret = $this->oneSqlSignle ( $sql );
		return $ret;
	}
}