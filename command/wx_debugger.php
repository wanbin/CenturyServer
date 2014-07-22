<?php
include_once '../Entry.php';
include_once PATH_DATAOBJ."/cache/UnderCoverRoomCache.php";
$UnderCache=new UnderCoverRoomCache();
// $UnderCache->initRoom(8,rand(100, 200));
echo $UnderCache->getInfo(1000,rand(100, 200));
// $UnderCache->add(array('uid'=>rand(100, 200),'lastlogin'=>time()));
?>