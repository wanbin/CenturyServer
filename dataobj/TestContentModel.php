<?php
/**
 * @author WanBin @date 2012-12-05
 * 测试数据库连接
 */
require_once PATH_DATAOBJ.'BaseModel.php';
class TestContentModel extends BaseModel {
	/**
	 * 得到所有记录
	 */
	protected function get() {
		$where = array ('gameuid' => $this->gameuid );
		$res = $this->hsSelectAll ( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
		$ret = array ();
		foreach ( $res as $key => $value ) {
			foreach ( $value as $jsonKey => $jsonValue )
				if (in_array ( $jsonKey, array ('count' ) )) {
					$value [$jsonKey] = json_decode ( $jsonValue, true );
				}
			$temid = $value ['count'];
			unset ( $value ['count'] );
			$ret [$temid] = $value;
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
	
	protected function getOneSingle($count) {
		$where = array ( 'gameuid' => $this->gameuid,'count' => $count );
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
	
	protected function updateOne($gameuid,$count, $content) {
		$where = array ( 'gameuid' => $this->gameuid,'count' => $count);
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
			if (in_array ( $key, array ('count') )) {
				$value = json_encode ( $value );
			}
			if (in_array ( $key, $fields )) {
				$insert [$key] = $value;
			}
		}
		return $this->hsInsert ( $this->getTableName (), $this->gameuid, $insert );
	}
	
	protected function addarr($content) {
		foreach ( $content as $key => $value ) {
			foreach ( $value as $jsonKey => $jsonValue ) {
				if (in_array ( $jsonKey, array ('count' ) )) {
					$content [$key] [$jsonKey] = json_encode ( $jsonValue );
				}
			}
		}
		return $this->hsMultiInsert ( $this->getTableName (), $this->gameuid, $content );
	}
	
	protected function init($gameuid,$count ) {
		$insert = array ( 'gameuid' => $this->gameuid,'count' => $count);
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
	
	protected function delOne( $gameuid,$count ) {
		$where = array ( 'gameuid' => $this->gameuid,'count' => $count);
		return $this->hsDelete ( $this->getTableName (), $this->gameuid, $where );
	}
	
	protected function getFields() {
		return 'gameuid,count';
	}
	
	protected function getTableName() {
		return "testContent";
	}
}