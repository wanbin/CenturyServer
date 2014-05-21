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
	
	protected function JoinRoom($id){
		$gameuid = $this->gameuid;
		$sql="select * from room where gameuid=$id";
		$ret=$this->oneSqlSignle($sql);
		if(empty($ret)||$ret['maxcount']<=$ret['newcount']){
			return false;
		}
		$sql="update room set nowcount=nowcount+1 where gameuid=$id;";
		$sql2 ="replace into user_rooms(gameuid,roomid,createtime)values($gameuid,$id,now());";
		$this->oneSql ( $sql . $sql2 );
		return true;
	}
	
	protected function GetRoomInfo($roomid) {
		$sql = "select * from room where gameuid=$roomid";
		$ret = $this->oneSqlSignle ( $sql );
		$sql2 = "select user_rooms.*,username from user_rooms,wx_account where wx_account.gameuid=user_rooms.gameuid and  roomid=$roomid order by createtime";
		$ret2 = $this->oneSql ( $sql2 );
		$ret ['room_user'] = $ret2;
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
	protected function getOne() {
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