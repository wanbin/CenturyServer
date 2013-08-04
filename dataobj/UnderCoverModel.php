<?php
/**
 * @author WanBin @date 2013-08-03
 * 微信用户表
 */
require_once PATH_DATAOBJ.'BaseModel.php';
class UnderCoverModel extends BaseModel {
	public function __construct( $uid = null) {
		parent::__construct();
		if (isset ( $uid )) {
			$res=$this->oneSql("select * from wx_account where uid='$uid'");
			if ( empty ( $res )) {
				$this->add ( array (
						'uid' => $uid,
						'regtime' => time ()
				) );
				$res=$this->oneSql("select * from wx_account where uid='$uid'");
			}
			$this->uid = $res ['uid'];
			$this->gameuid = $res ['gameuid'];
		}
	}
	
	public function Log($content) {
		$this->hsInsert ( $this->getTableNameLog (), array (
				'gameuid'=>$this->gameuid,
				'content' => $content,
				'time' => time ()
		) );
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
		$where = array ( 'gameuid' => $this->gameuid );
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
	
	protected function updateOne( $content,$gameuid) {
		$where = array ( 'gameuid' => $this->gameuid);
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
		foreach ( $content as $key => $value ) {
			if (in_array ( $key, array () )) {
				$value = json_encode ( $value );
			}
			if (in_array ( $key, $fields )) {
				$insert [$key] = $value;
			}
		}
		return $this->hsInsert ( $this->getTableName (),$insert );
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
		return 'gameuid,uid,regtime';
	}
	
	protected function getTableName() {
		return "wx_account";
	}
	protected function getTableNameLog() {
		return "wx_log";
	}
}