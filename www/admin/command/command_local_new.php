<?php
//读某个人的信息，把这个人的未读标记去掉
$name=$_REQUEST['name'];
$gameid=$_REQUEST['gameid'];
$img=$_REQUEST['img'];
include_once PATH_HANDLER . 'LocalHandler.php';
$words = new LocalHandler($uid);
$id=$words->create($name, $gameid, $img);
// $words->newWords($content, $type) ;
echo "创建成功".$id;