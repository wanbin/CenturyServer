<?php
$def_date=date("Y-m-d H:i:s",time()+60);
$id=$_REQUEST['id'];
$update=false;
if($id>0){
	include_once PATH_HANDLER.'PageHandler.php';
	$game=new PageHandler($uid);
	$ret=$game->getPageOne($id);
	$def_date=date("Y-m-d H:i:s",$ret['showtime']);
	$update=true;
}