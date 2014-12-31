<?php
include_once PATH_HANDLER.'GameHandler.php';
$game=new GameHandler($uid);
$ret=$game->getGameList(1);
foreach ($ret as $key=>$value){
	$ret[$key]['showtime']=date("Y-m-d H:i:s",$value['showtime']);
	$ret[$key]['status']=$value['showtime']>time()?"未展示":"已经展示";
	if(!empty($value['homeimg'])){
		$ret[$key]['homeimg'].='!50X50';
	}
}

