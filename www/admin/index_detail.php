<?php
session_start ();
include_once '../../define.php';
require_once PATH_ROOT."framework/MooPHP/MooPHP.php";



$showPage=$_REQUEST['showpage'];

$pageArr=array('punish_edit','punish_shenhe');


if(in_array($showPage, $pageArr)){
	include_once PATH_ADMIN_CONTROL."/$showPage.php";
	include( Mootemplate( $showPage,true) );
}
else{
	echo "页面加载失败";
}


