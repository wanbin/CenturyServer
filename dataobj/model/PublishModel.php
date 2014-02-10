<?php
/**
 * @author WanBin @date 2013-12-30
 * 惩罚与真心话
 */
require_once PATH_MODEL.'BaseModel.php';
class PublishModel extends BaseModel {
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
	 * 审核词汇
	 * @param unknown_type $id
	 * @param unknown_type $type
	 */
	public function changeShow($id,$type){
		$sql="update user_publish set isshow=$type where id=$id";
		return	$this->oneSql($sql);
	}
	
	
	protected function delSample(){
		$sql = "select id,content from user_publish";
		$ret = $this->oneSql ( $sql );
		$tem = array ();
		foreach ( $ret as $key => $value ) {
			if (in_array ( $value ['content'], $tem )) {
				$this->oneSql ( "delete from user_publish where id=" . $value ['id'] );
			} else {
				$tem [] = $value ['content'];
			}
		}
		file_put_contents("new.log", print_R($tem,true));
		exit();
		
	}
	
	protected function getRandomOne(){
		$sql="select count(*) count from user_publish";
		$ret=$this->oneSql($sql);
		$index=rand(0,$ret[0]['count']);
		$res=$this->oneSql("select user_publish.*,username,date(FROM_UNIXTIME(time)) sendtime from user_publish,wx_account where wx_account.gameuid=user_publish.gameuid and isshow=1  order by id desc limit $index,1");
		return $res;
	}
	
	protected function getPage($page) {
// 		$this->delSample();
		$where = array (
				'isshow' => 1
		);
		$res = $this->oneSql ( "select user_publish.*,username,date(FROM_UNIXTIME(time)) sendtime from user_publish,wx_account where wx_account.gameuid=user_publish.gameuid and isshow=1  order by id desc limit $page,".PAGECOUNT );
		return $res;
	}
	
	protected  function getCount(){
		$ret=$this->oneSqlSignle("select count(*) count from user_publish where isshow=1 and type=2");
		return $ret['count'];
	}
	
	/**
	 * 返回需要审核的词汇
	 * @param unknown_type $page
	 * @return unknown
	 */
	protected function getPageShenHe($page) {
		$where = array (
				'isshow' => 0
		);
		$res=$this->oneSql("select user_publish.*,username,date(FROM_UNIXTIME(time)) sendtime from user_publish,wx_account where wx_account.gameuid=user_publish.gameuid and isshow=0 order by id desc limit 30");
		return $res;
	}
	
	
	/**
	 * 添加喜欢
	 * @param unknown_type $id
	 * @param unknown_type $like
	 * @param unknown_type $dislike
	 * @param unknown_type $collete
	 */
	protected function addLike($id,$like=0,$dislike=0){
		$sql="update user_publish set `like`=`like`+$like,dislike=dislike+$dislike where id=$id";
		if(DEBUG)
		{
			file_put_contents("deb.log", $sql);
		}
		$this->oneSql($sql);
	}
	
	
	/**
	 * 得到一条记录
	 *
	 * @param $id unknown_type
	 * @return Ambigous <boolean, multitype:, multitype:multitype: >
	 */
	protected function getOne($id) {
		$where = array ('id' =>$id );
		$res = $this->hsSelectAll ( $this->getTableName (), $this->getFields (), $where );
		return $res[0];
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
		return 'id,gameuid,content,time,type,like,dislike,isshow';
	}
	
	protected function getTableName() {
		return "user_publish";
	}
}