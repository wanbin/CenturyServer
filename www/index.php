<?php
session_start ();
include_once '../define.php';
require_once PATH_ROOT."framework/MooPHP/MooPHP.php";

include_once PATH_CONTROL.'/header.php';
include( Mootemplate( 'header' ) );


if (isset ( $_REQUEST ['username'] )) {
	$_SESSION ['username'] = $_REQUEST ['username'];
	$uid=$_SESSION ['username'];
}

$showPage=$_REQUEST['showpage'];

$pageArr=array('help','home','gamenow','punish','punishadd','punishshenhe','helpimage');

if(in_array($showPage, $pageArr)){
	include_once PATH_CONTROL."/$showPage.php";
	include( Mootemplate( $showPage) );
}
else{
	include_once PATH_CONTROL.'/index.php';
	include( Mootemplate( 'index' ) );
}


include( Mootemplate( 'footer' ) );