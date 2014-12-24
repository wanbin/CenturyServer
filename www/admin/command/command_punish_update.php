<?php
//读某个人的信息，把这个人的未读标记去掉
$id=$_REQUEST['id'];
$content=$_REQUEST['content'];
include_once PATH_HANDLER . 'PunishHandler.php';
$punish = new PunishHandler($uid);
$punish->updatePunish($id,$content );
echo 1;