<?php
include_once PATH_HANDLER . 'MailHandler.php';
$mail = new MailHandler ( $uid );
$ret = $mail->getMailList(1,true);
