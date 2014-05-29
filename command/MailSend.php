<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/MailHandler.php';
class MailSend extends BaseCommand {
	protected function executeEx($params) {
		$uid = $params ['uid'];
		$mail = new MailHandler ( $this->uid );
		$sendto=$params['sendto'];
		$content=$params['content'];
		$ret = $mail->mailSend($sendto, $content);
		return $this->reutrnDate ( COMMAND_ENPTY ,$ret);
	}
}