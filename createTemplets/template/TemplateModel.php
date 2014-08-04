<?php
/**
 * @author {Author} @date {Date}
 * TemplatenContent
 */
require_once PATH_MODEL.'BaseModel.php';
class TemplateModel extends BaseModel {
	/**
	 * 得到所有记录
	 */
	protected function get() {
		$where = array ('gameuid' => $this->gameuid );
		$res = $this->hsSelectAll ( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
		$ret = array ();
		foreach ( $res as $key => $value ) {
			foreach ( $value as $jsonKey => $jsonValue )
				if (in_array ( $jsonKey, array ({strJson} ) )) {
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
		$where = array ( {strWhere}  );
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
	
	protected function updateOne( $content,{strUnion}) {
		$where = array ( {strWhere} );
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
			if (in_array ( $key, array ({strJson}) )) {
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
				if (in_array ( $jsonKey, array ({strJson} ) )) {
					$content [$key] [$jsonKey] = json_encode ( $jsonValue );
				}
			}
		}
		return $this->hsMultiInsert ( $this->getTableName (), $this->gameuid, $content );
	}
	
	protected function init({strUnion} ) {
		$insert = array ( {strWhere} );
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
	
	protected function delOne( {strUnion} ) {
		$where = array ( {strWhere} );
		return $this->hsDelete ( $this->getTableName (), $this->gameuid, $where );
	}
	
	protected function getFields() {
		return 'templatefields';
	}
	
	protected function getTableName() {
		return "templatetablename";
	}
}