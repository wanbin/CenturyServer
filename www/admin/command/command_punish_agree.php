<?php
//读某个人的信息，把这个人的未读标记去掉
$id=$_REQUEST['id'];
$type=$_REQUEST['type'];
include_once PATH_HANDLER . 'PunishHandler.php';
$punish = new PunishHandler($uid);
$punish->changeShow($id, $type);
if($type==1){
	echo "审核通过";
}else{
	echo "未审核通过";
}