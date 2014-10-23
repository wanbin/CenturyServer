<?php
session_start ();
include_once '../define.php';
require_once PATH_ROOT."framework/MooPHP/MooPHP.php";



$showPage=$_REQUEST['showpage'];

$pageArr=array('adminpunish','adminpunishedit');


if(in_array($showPage, $pageArr)){
	include_once PATH_CONTROL."/$showPage.php";
	include( Mootemplate( $showPage) );
}
else{
	echo "页面加载失败";
}


