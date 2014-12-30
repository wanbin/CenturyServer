<?php
//读某个人的信息，把这个人的未读标记去掉
$id=$_REQUEST['_id'];
$link=$_REQUEST['link'];
$name=$_REQUEST['name'];
$key=$_REQUEST['key'];
include_once PATH_HANDLER.'PageHandler.php';
$page=new PageHandler($uid);
$id=$page->updateLink($id, $key, $name, $link);
echo "更新链表成功,ID:".$id;