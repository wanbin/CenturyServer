<?php
//读某个人的信息，把这个人的未读标记去掉
$content=stripslashes($_REQUEST['content']);
$sendtime=$_REQUEST['sendtime'];
$title=$_REQUEST['title'];
$type=$_REQUEST['type'];
$keyname=$_REQUEST['keyname'];

include_once PATH_HANDLER.'ArticleHandler.php';
$game=new ArticleHandler($uid);
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

$id=$game->newGame($title, $homeurl, $content, $timeint,$type,$keyname);


if ($type == 1) {
	$pushcontent = "最新游戏 $title 已经上线，快来围观吧！爱上聚会";
	// 读某个人的信息，把这个人的未读标记去掉
	include_once '../model/PushBase.php';
	$push = new PushBase ();
	$id = $push->newpush ( $pushcontent, $timeint );
}

echo $content."发送成功,ID:".$id;






