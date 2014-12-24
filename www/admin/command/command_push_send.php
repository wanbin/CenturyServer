<?php
//读某个人的信息，把这个人的未读标记去掉
$content=$_REQUEST['content'];
$sendtime=$_REQUEST['sendtime'];
include_once '../model/PushBase.php';
$push=new PushBase();
$timeint=strtotime($sendtime);
if($timeint==0){
	echo "错误的日期";
	return;
}
if(empty($content)){
	echo "错误的内容";
	return;
}
echo $timeint;
$id=$push->newpush($content,$timeint);


echo $content."发送成功,ID:".$id;