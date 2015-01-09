<?php
$page=isset($_REQUEST['page'])?$_REQUEST['page']:1;
$type=$_REQUEST['type'];
include_once PATH_HANDLER.'ArticleHandler.php';
$game=new ArticleHandler($uid);
$ret=$game->getGameList($page,$type);
foreach ($ret as $key=>$value){
	$ret [$key] ['showtime'] = date ( "Y-m-d H:i:s", $value ['showtime'] );
	$ret [$key] ['status'] = $value ['showtime'] > time () ? "未展示" : "已经展示";
	$ret [$key] ['readinfo'] = $game->getReadInfo ( $value ['_id'] );
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

