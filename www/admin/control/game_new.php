<?php
$def_date=date("Y-m-d H:i:s",time()+60);
$id=$_REQUEST['id'];
$update=false;
if($id>0){
	include_once PATH_HANDLER.'GameHandler.php';
	$game=new GameHandler($uid);
	$ret=$game->getOne($id);
	$def_date=date("Y-m-d H:i:s",$ret['showtime']);
	$update=true;
}