<?php
session_start ();
include_once '../define.php';
require_once PATH_ROOT."framework/MooPHP/MooPHP.php";

include_once PATH_CONTROL.'/header.php';
include( Mootemplate( 'header' ) );

$showPage=$_REQUEST['showpage'];
$pageArr=array('help','home','gamenow','punish','punishadd','adminpunish','helpimage','game_list');





if (isset ( $_REQUEST ['username'] )) {
	$_SESSION ['username'] = $_REQUEST ['username'];
	$uid=$_SESSION ['username'];
}

if (isset ( $_SESSION ['username'] )) {
	$uid = $_SESSION ['username'];
} else {
	$uid = isset ( $_REQUEST ['username'] ) ? $_REQUEST ['username'] : "";
	// 如果用户没有登录，创建个新用户吧
	if (empty ( $uid )) {
		$uid = date ( "Y_M_D_H_i_s" ) . rand ( 0, 10000000 );
	}
	if (! empty ( $uid )) {
		$_SESSION ['username'] = $uid;
	}
}



if(in_array($showPage, $pageArr)){
	include_once PATH_CONTROL."/$showPage.php";
	include( Mootemplate( $showPage) );
}
else{
	include_once PATH_CONTROL.'/index.php';
	include( Mootemplate( 'index' ) );
}









include( Mootemplate( 'footer' ) );