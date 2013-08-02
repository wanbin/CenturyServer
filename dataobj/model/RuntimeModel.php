<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户正在生产的信息表
 */
require_once '../BaseModel.php';
class RuntimeModel extends BaseModel {
	
	/**
	 * 得到一条记录
	 *
	 * @param $id unknown_type
	 * @return Ambigous <boolean, multitype:, multitype:multitype: >
	 */
	protected function get() {
		$where = array (
				'gameuid' => $this->gameuid
		);
		$res = $this->hsSelectAll ( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
		return $res;
	}
	
	/**
	 * 更新信息
	 *
	 * @param $content unknown_type
	 * @return Ambigous <boolean, number, multitype:>
	 */
	protected function update($content) {
		$where = array (
				'gameuid' => $this->gameuid
		);
		$content ['data'] = json_encode ( $content ['data'] );
		$content ['updatetime'] = time ();
		$res = $this->hsUpdate ( $this->getTableName (), $this->gameuid, $content, $where );
		return $res;
	}
	protected function init() {
		$insert = array (
				'gameuid' => $this->gameuid,
				'updatetime' => time ()
		);
		return $this->hsInsert ( $this->getTableName (), $this->gameuid, $insert );
	}
	
	protected function getFields() {
		return 'gameuid,data,updatetime';
	}
	
	protected function getTableName() {
		return "user_runtime";
	}
}