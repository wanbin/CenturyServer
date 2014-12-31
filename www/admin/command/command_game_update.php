<?php
//读某个人的信息，把这个人的未读标记去掉
$content=stripslashes($_REQUEST['content']);
$sendtime=$_REQUEST['sendtime'];
$title=$_REQUEST['title'];
$id=$_REQUEST["_id"];

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
preg_match_all("/http(.*)png/",$content,$matchs);
//取第一张图片
$homeurl=$matchs[0][0];

$id=$game->updateGame($id,$title, $homeurl, $content, $timeint);
echo "更新成功,ID:".$id;