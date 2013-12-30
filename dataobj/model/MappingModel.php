<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户Mapping映射表
 */
require_once 'BaseModel.php';
class MappingModel extends BaseModel {
	/**
	 * 得到多条记录
	 */
	protected function get($uidArr = array()) {
		$res = $this->hsSelectIn ( $this->getTableName (), $this->gameuid, $this->getFields (), $uidArr );
		return $res;
	}
	/**
	 * 得到一条记录
	 *
	 * @param $id unknown_type       	
	 * @return Ambigous <boolean, multitype:, multitype:multitype: >
	 */
	protected function getOne() {
		$where = array ('gameuid' => $this->gameuid );
		$res = $this->hsSelectOne ( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
		return $res;
	}
	protected function getOneUid($uid) {
		$where = array ('uid' => $uid );
		$res = $this->hsSelectOne( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
		return $res;
	}
	/**
	 * 更新信息
	 *
	 * @param $content unknown_type       	
	 * @return Ambigous <boolean, number, multitype:>
	 */
	
	protected function updateOne($uid, $gameuid) {
		$where = array ('gameuid' => $this->gameuid );
		$res = $this->hsUpdate ( $this->getTableName (), $this->gameuid, $uid, $where );
		return $res;
	}
	
		/**
	 * 添加一条信息
	 *
	 * @param $content unknown_type       	
	 * @return Ambigous <boolean, number, multitype:>
	 */
	protected function add($uid) {
		$insert ['gameuid'] = $this->gameuid;
		$insert ['uid'] = $uid;
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
	
	protected function delOne( $uid ) {
		$where = array ( 'uid' => $uid);
		return $this->hsDelete ( $this->getTableName (), $this->gameuid, $where );
	}
	
	protected function getFields() {
		return 'gameuid,uid';
	}
	
	protected function getTableName() {
		return "user_mapping";
	}
}