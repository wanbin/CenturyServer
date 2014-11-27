<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/MailHandler.php';
class MailList extends BaseCommand {
	protected function executeEx($params) {
		$mail = new MailHandler ( $this->uid );
		$page=isset($params['page'])?$params['page']:1;
		$ret = $mail->getMailList($page);
		return $this->reutrnDate ( COMMAND_ENPTY ,$ret);
	}
}