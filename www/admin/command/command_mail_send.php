<?php
//读某个人的信息，把这个人的未读标记去掉
$gameuid=$_REQUEST["gameuid"];
$content=$_REQUEST["content"];

include_once PATH_HANDLER.'MailHandler.php';
$mail=new MailHandler($uid);
$ret=$mail->mailSend(-1,$gameuid, $content);

echo "发送邮件 $ret 成功";
