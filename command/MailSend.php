<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/MailHandler.php';
class MailSend extends BaseCommand {
	protected function executeEx($params) {
		$mail = new MailHandler ( $this->gameuid );
		$sendto=$params['sendto'];
		$content=$params['content'];
		$ret = $mail->mailSend($sendto,$mail->gameuid, $content);
		return $this->reutrnDate ( COMMAND_ENPTY ,$ret);
	}
}