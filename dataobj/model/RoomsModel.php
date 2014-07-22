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
		$sql = "replace into room(gameuid,maxcount,nowcount,createtime)values($gameuid,$maxcount,1,now());";
		$sql2 = "replace into user_rooms(gameuid,roomid,createtime)values($gameuid,$gameuid,now());";
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
		$sql2 ="replace into user_rooms(gameuid,roomid,content,createtime)values($gameuid,$id,'已经加入游戏，还未开始',now());";
		$this->oneSql ( $sql . $sql2 );
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
	
	/**
	 * 得到所有记录
	 */
	protected function get() {
		$where = array ('gameuid' => $this->gameuid );
		$res = $this->hsSelectAll ( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
		$ret = array ();
		foreach ( $res as $key => $value ) {
			foreach ( $value as $jsonKey => $jsonValue )
				if (in_array ( $jsonKey, array ( ) )) {
					$value [$jsonKey] = json_decode ( $jsonValue, true );
				}
			$temid = $value ['templateid'];
			unset ( $value ['templateid'] );
			$ret [$value ['templateid']] = $value;
		}
		return $ret;
	}
	
	/**
	 * 得到一条记录
	 *
	 * @param $id unknown_type
	 * @return Ambigous <boolean, multitype:, multitype:multitype: >
	 */
	protected function getOne($id) {
		$where = array ('gameuid' => $this->gameuid );
		$res = $this->hsSelectAll ( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
		return $res;
	}
	
	protected function getOneSingle($templateid) {
		$where = array ( 'id' => $id );
		$res = $this->hsSelectOne ( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
		return $res;
	}
	/**
	 * 更新信息
	 *
	 * @param $content unknown_type
	 * @return Ambigous <boolean, number, multitype:>
	 */
	protected function update($content) {
		$where = array ('gameuid' => $this->gameuid );
		$res = $this->hsUpdate ( $this->getTableName (), $this->gameuid, $content, $where );
		return $res;
	}
	
	protected function updateOne( $content,$id) {
		$where = array ( 'id' => $id);
		$res = $this->hsUpdate ( $this->getTableName (), $this->gameuid, $content, $where );
		return $res;
	}
	
		/**
	 * 添加一条信息
	 *
	 * @param $content unknown_type
	 * @return Ambigous <boolean, number, multitype:>
	 */
	protected function add($content) {
		$fields = explode ( ',', $this->getFields () );
		$insert ['gameuid'] = $this->gameuid;
		foreach ( $content as $key => $value ) {
			if (in_array ( $key, array () )) {
				$value = json_encode ( $value );
			}
			if (in_array ( $key, $fields )) {
				$insert [$key] = $value;
			}
		}
		return $this->hsInsert ( $this->getTableName (), $insert );
	}
	
	protected function addarr($content) {
		foreach ( $content as $key => $value ) {
			foreach ( $value as $jsonKey => $jsonValue ) {
				if (in_array ( $jsonKey, array ( ) )) {
					$content [$key] [$jsonKey] = json_encode ( $jsonValue );
				}
			}
		}
		return $this->hsMultiInsert ( $this->getTableName (), $this->gameuid, $content );
	}
	
	protected function init($id ) {
		$insert = array ( 'id' => $id);
		return $this->hsInsert ( $this->getTableName (), $this->gameuid, $insert );
	}
	/**
	 * 删除一条信息
	 *
	 * @return number
	 */
	protected function del() {
		$where = array ('gameuid' => $this->gameuid );
		return $this->hsDelete ( $this->getTableName (), $this->gameuid, $where );
	}
	
	protected function delOne( $id ) {
		$where = array ( 'id' => $id);
		return $this->hsDelete ( $this->getTableName (), $this->gameuid, $where );
	}
	
	protected function getFields() {
		return 'id,gameuid,roomid,content,updatetime';
	}
	
	protected function getTableName() {
		return "user_rooms";
	}
}