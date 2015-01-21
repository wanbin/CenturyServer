<?php
require_once PATH_CACHE . 'MailCache.php';
/**
 * @author wanhin
 * 用户行为统计类
 *
 */
class MailHandler extends MailCache{
	public function getUnreadCount() {
		return parent::getUnreadCount ();
	}
	
	public function getMailList($page=1,$isgm=false) {
		$ret=parent::getMailList ($page,$isgm);
		include_once PATH_HANDLER . 'AccountHandler.php';
		$account = new AccountHandler ( $this->uid );
		foreach ($ret as $key=>$value){
			$tem=$account->getAccountByGameuid($value['fromgameuid']);
			$ret[$key]['username']=$tem['username'];
			$ret[$key]['photo']=$tem['photo'];
			$ret[$key]['read']=$tem['read']==1?true:false;
			$ret[$key]['time']=$this->getTimeStr($value['time']);
		}
		return $ret;
	}
	
	public function mailSend($from,$sendto,$content){
		return parent::SendMail($from,$sendto, $content);
	}
	/**
	 * 系统发送的不重复邮件
	 * @param unknown_type $sendto
	 * @param unknown_type $content
	 * @return boolean
	 */
	public function systemSendNotDobule($sendto,$content){
		$key = md5 ( $content );
		$rediska = new Rediska ();
		$key = "Mail_System_Has_Send_" . $key;
		$hash = new Rediska_Key_Hash ( $key );
		if(!$hash->exists($sendto)){
			$this->mailSend(-1, $sendto, $content);
			$hash->increment($sendto);
			return true;	
		}
		return false;
	}
	
	public function mailDel($mailid){
		return parent::mailDel($mailid);
	}
}