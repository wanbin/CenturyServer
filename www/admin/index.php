<?php
session_start ();
include_once '../../define.php';
require_once PATH_ROOT."framework/MooPHP/MooPHP.php";

include_once PATH_ADMIN_CONTROL.'/header.php';
include( Mootemplate( 'header' ,true) );

$showPage=$_REQUEST['showpage'];
$pageAdminArr = array (
		'admin',
		'punish_edit',
		'punish_shenhe',
		'index',
		'push_new',
		'push_edit',
		'login',
		'article_new',
		'article_edit',
		'page_new',
		'page_edit',
		'page_link',
		'wx_edit',
		'wx_new',
		'mail_new',
		'mail_list',
		'version_edit',
		'words_edit',
		'action_edit',
		'guess_edit',
		'local_edit',//本地显示游戏管理
		'wx_message' 
);




// exit();
if(in_array($showPage, $pageAdminArr)&&isset($_SESSION['adminname'])){
	include_once PATH_ADMIN_CONTROL."/base.php";
	include_once PATH_ADMIN_CONTROL."/$showPage.php";
	include( Mootemplate( $showPage,true) );
}
else{
	include_once PATH_ADMIN_CONTROL.'/index.php';
	include( Mootemplate( 'login',true ) );
}


if (isset ( $_REQUEST ['username'] )) {
	$_SESSION ['username'] = $_REQUEST ['username'];
	$uid=$_SESSION ['username'];
}





include( Mootemplate( 'footer',true) );