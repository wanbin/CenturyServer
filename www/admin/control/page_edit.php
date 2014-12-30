<?php
include_once PATH_HANDLER.'PageHandler.php';
$page=new PageHandler($uid);
$ret=$page->getPageList(1);
foreach ($ret as $key=>$value){
	$ret[$key]['time']=date("Y-m-d H:i:s",$value['time']);
	$ret[$key]['status']=$value['showtime']>time()?"未展示":"已经展示";
}

