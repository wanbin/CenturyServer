<?php
include_once PATH_HANDLER.'ArticleHandler.php';
$game=new ArticleHandler($uid);
$ret=$game->getGameList(1,0);
foreach ($ret as $key=>$value){
	$ret[$key]['showtime']=date("Y-m-d H:i:s",$value['showtime']);
	$ret[$key]['status']=$value['showtime']>time()?"未展示":"已经展示";
	if(!empty($value['homeimg'])){
		$ret[$key]['homeimg'].='!50X50';
	}
	if($value['type']==0){
		$ret[$key]['typename']='未分类';
	}else if($value['type']==1){
		$ret[$key]['typename']='游戏';
	}else if($value['type']==2){
		$ret[$key]['typename']='帮助';
	}
	
}

