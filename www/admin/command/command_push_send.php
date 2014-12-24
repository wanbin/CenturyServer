<?php
//读某个人的信息，把这个人的未读标记去掉
$content=$_REQUEST['content'];
include_once '../model/PushBase.php';
$push=new PushBase();
$id=$push->newpush($content);


echo $content."发送成功,ID:".$id;