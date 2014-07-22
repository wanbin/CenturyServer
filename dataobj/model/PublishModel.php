<?php
/**
 * @author WanBin @date 2013-12-30
 * 惩罚与真心话
 */
require_once PATH_MODEL.'BaseModel.php';
class PublishModel extends BaseModel {
	/**
	 * 审核词汇
	 * @param unknown_type $id
	 * @param unknown_type $type
	 */
	public function changeShow($id,$type){
		$sql="update user_publish set isshow=$type where id=$id";
		return	$this->oneSql($sql);
	}
	
	public function getCollectType($id){
		$sql = "select type from user_publish where id=$id and gameuid=" . $this->gameuid;
		$ret = $this->oneSqlSignle ( $sql );
		return $ret ['type'];
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
	
	protected function getRandomOne($type){
		$sql="select count(*) count from user_publish where isshow=1";
		$ret=$this->oneSql($sql);
		$index=rand(0,$ret[0]['count']);
		$res=$this->oneSql("select user_publish.*,username,date(FROM_UNIXTIME(time)) sendtime from user_publish,wx_account where wx_account.gameuid=user_publish.gameuid and isshow=1  order by id desc limit $index,1");
		return $res;
	}
	
	protected function getPage($page) {
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
		$this->oneSql($sql);
	}
	
	
	protected function getFields() {
		return 'id,gameuid,content,time,type,like,dislike,isshow';
	}
	
	protected function getTableName() {
		return "user_publish";
	}
}