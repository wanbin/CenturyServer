<?php
/**
 * @author WanBin @date 2014-01-14
 * 用户行为LOG
 */
require_once PATH_MODEL.'BaseModel.php';
class MailModel extends BaseModel {
	/**
	 * 得到所有记录
	 */
	protected function get() {
		$where = array ('gameuid' => $this->gameuid );
		$res = $this->hsSelectAll ( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
		$ret = array ();
		foreach ( $res as $key => $value ) {
			foreach ( $value as $jsonKey => $jsonValue )
				if (in_array ( $jsonKey, array ('content' ) )) {
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
	
	
	protected function getOneMail(){
		$sql="select * from user_mail where is_read=0 and gameuid=".$this->gameuid." limit 1";
		$ret=$this->oneSqlSignle($sql);
		if(empty($ret))
			return ;
		$this->readMail($ret['id']);
		return $ret;
	}
	
	
	protected function SendMail($sendto,$content){
		$gameuid=$this->gameuid;
		$time=time();
		$sql="insert into user_mail(gameuid,fromgameuid,time,content) values($sendto,$gameuid,$time,'$content')";
		$ret=$this->oneSql($sql);
		return $ret;
	}
	
	protected function readMail($id) {
		if ($id > 0) {
			$sql = "update user_mail set is_read=1 where id=$id";
			$this->oneSql ( $sql );
		}
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
				if (in_array ( $jsonKey, array ('content' ) )) {
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
		return 'id,gameuid,fromgameuid,time,content,is_read';
	}
	
	protected function getTableName() {
		return "user_mail";
	}
}