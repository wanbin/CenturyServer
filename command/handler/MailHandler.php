<?php
require_once PATH_CACHE . 'MailCache.php';
/**
 * @author wanhin
 * 用户行为统计类
 *
 */
class MailHandler extends MailCache{
	public function getOneMail() {
		return parent::getOneMail ();
	}
	public function getMailList($page=1) {
		$ret=parent::getMailList ($page);
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
}