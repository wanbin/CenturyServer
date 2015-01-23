<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/MailHandler.php';
class MailDel extends BaseCommand {
	protected function executeEx($params) {
		$mail = new MailHandler ( $this->gameuid );
		$mailid=$params['mailid'];
		$ret = $mail->mailDel($mailid);
		return $this->returnDate ( COMMAND_ENPTY ,array('mailid'=>$mailid,'status'=>$ret));
	}
}