<?php
//读某个人的信息，把这个人的未读标记去掉
$content=$_REQUEST['content'];
$type=$_REQUEST['type'];
include_once PATH_HANDLER . 'PunishHandler.php';
$punish = new PunishHandler($uid);
if(!in_array($type, array(1,2,3))){
	$type=1;
}
$contentarr=explode("\n", $content);
foreach ($contentarr as $key=>$value){
	if(!empty($value)){
		$punish->newPublish(trim($value), $type);
	}
}
echo "success add ".count($contentarr);

