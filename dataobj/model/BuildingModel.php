<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户建筑表
 */
require_once '../BaseModel.php';
class BuildingModel extends BaseModel {

	/**
	 * 得到一条记录
	 *
	 * @param $id unknown_type
	 * @return Ambigous <boolean, multitype:, multitype:multitype: >
	 */
	protected function getOne() {
		$where = array ('gameuid' => $this->gameuid );
		$res = $this->hsSelectOne( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
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
	
	protected function init($gameuid ) {
		$insert = array ( 'gameuid' => $this->gameuid);
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
	
	protected function delOne( $gameuid ) {
		$where = array ( 'gameuid' => $this->gameuid);
		return $this->hsDelete ( $this->getTableName (), $this->gameuid, $where );
	}
	
	protected function getFields() {
		return 'gameuid,crop,cropupdatetime';
	}
	
	protected function getTableName() {
		return "user_building";
	}
}