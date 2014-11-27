<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/MailHandler.php';
class MailList extends BaseCommand {
	protected function executeEx($params) {
		$mail = new MailHandler ( $this->uid );
		$page=isset($params['page'])?$params['page']:1;
// 		$mail->mailSend(-1, $mail->gameuid, "欢迎欢迎");
		$ret = $mail->getMailList($page);
		return $this->reutrnDate ( COMMAND_ENPTY ,$ret);
	}
}