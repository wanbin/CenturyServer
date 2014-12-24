<?php
//读某个人的信息，把这个人的未读标记去掉
$id=$_REQUEST['id'];
include_once PATH_HANDLER . 'PunishHandler.php';
$punish = new PunishHandler($uid);
$punish->changeShow ( $id, 3 );
echo 1;
