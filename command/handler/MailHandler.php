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
	public function mailSend($from,$sendto,$content){
		return parent::SendMail($from,$sendto, $content);
	}
}