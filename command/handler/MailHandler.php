<?php
require_once PATH_CACHE . 'MailCache.php';
/**
 * @author wanhin
 * 用户行为统计类
 *
 */
class MailHandler extends MailCache{
	public function addMail($gameuid,$fromGameuid,$content){
		$content=array(
				'gameuid'=>$gameuid,
				'fromgameuid' => $fromGameuid,
				'time' => time(),
				'content' => $content,
				'is_read'=>0
		);
		$this->add($content);
	}
	public function getOneMail() {
		return parent::getOneMail ();
	}
	public function mailSend($sendto,$content){
		return parent::SendMail($sendto, $content);
	}
}