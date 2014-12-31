<?php
//读某个人的信息，把这个人的未读标记去掉
$content=stripslashes($_REQUEST['content']);
$title=$_REQUEST['title'];

include_once PATH_HANDLER.'PageHandler.php';
$page=new PageHandler($uid);

if(empty($content)){
	echo "错误的内容";
	return;
}
if(empty($title)){
	echo "错误的标题";
	return;
}
$id=$page->newPage($title, $content);
echo "发布界面成功,ID:".$id;