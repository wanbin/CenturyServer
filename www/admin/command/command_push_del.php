<?php
//读某个人的信息，把这个人的未读标记去掉
$content=$_REQUEST['pushid'];
print_r($content);
include_once '../model/PushBase.php';
$push=new PushBase();
$ret=$push->del($content);
echo "删除 $content 成功";

