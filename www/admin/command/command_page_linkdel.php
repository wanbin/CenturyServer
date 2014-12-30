<?php
//读某个人的信息，把这个人的未读标记去掉
$linkid=$_REQUEST['linkid'];

include_once PATH_HANDLER.'PageHandler.php';
$page=new PageHandler($uid);
$id=$page->delLink($linkid);
echo "删除链表成功,ID:".$linkid;