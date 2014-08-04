<?php
/**
 * @author WanBin @date 2014-01-14
 * 用户行为LOG
 */
require_once PATH_MODEL.'BaseModel.php';
class MailModel extends BaseModel {
	

	protected function getOneMail(){
		$sql="select * from user_mail where is_read=0 and gameuid=".$this->gameuid." limit 1";
		$ret=$this->oneSqlSignle($sql);
		if(empty($ret))
			return ;
		$this->readMail($ret['id']);
		return $ret;
	}
	
	protected function SendMail($from,$sendto,$content){
		$time=time();
		$sql="insert into user_mail(gameuid,fromgameuid,time,content) values($sendto,$from,$time,'$content')";
		$ret=$this->oneSql($sql);
		return $ret;
	}
	
	protected function readMail($id) {
		if ($id > 0) {
			$sql = "update user_mail set is_read=1 where id=$id";
			$this->oneSql ( $sql );
		}
	}

}