<?php
//读某个人的信息，把这个人的未读标记去掉
$key=$_REQUEST['key'];
$des=$_REQUEST['des'];

include_once PATH_HANDLER.'PageHandler.php';
$page=new PageHandler($uid);
if(empty($key)){
	echo "关键字错误";
	return;
}
if(empty($des)){
	echo "描述不能为空";
	return;
}

$id=$page->newLink($key, $des,"");
echo "添加链表成功,ID:".$id;