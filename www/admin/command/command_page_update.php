<?php
//读某个人的信息，把这个人的未读标记去掉
$content=$_REQUEST['content'];
$title=$_REQUEST['title'];
$id=$_REQUEST['_id'];

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
$id=$page->updatePage($id, $title, $content);
echo "更新成功,ID:".$id;