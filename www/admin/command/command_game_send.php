<?php
//读某个人的信息，把这个人的未读标记去掉
$content=stripslashes($_REQUEST['content']);
$sendtime=$_REQUEST['sendtime'];
$title=$_REQUEST['title'];

include_once PATH_HANDLER.'GameHandler.php';
$game=new GameHandler($uid);
$timeint=strtotime($sendtime);
if($timeint==0){
	echo "错误的日期";
	return;
}
if(empty($content)){
	echo "错误的内容";
	return;
}
if(empty($title)){
	echo "错误的标题";
	return;
}
echo $timeint;
$id=$game->newGame($title, "", $content, $timeint);
echo $content."发送成功,ID:".$id;