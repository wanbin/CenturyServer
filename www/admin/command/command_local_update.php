<?php
//读某个人的信息，把这个人的未读标记去掉
$type=$_REQUEST['type'];
$name = $_REQUEST ['name'];
$id = $_REQUEST ['id'];
$gameid = $_REQUEST ['gameid'];
$img = $_REQUEST ['img'];
$sort = $_REQUEST ['sort'];
$des = $_REQUEST ['des'];
include_once PATH_HANDLER . 'LocalHandler.php';
$words = new LocalHandler ( $uid );
$words->update($id,$type, $name, $gameid, $img,$des, $sort);
echo "更新成功" . rand ( 1, 900 );