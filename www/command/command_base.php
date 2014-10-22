<?php
session_start ();
include '../../define.php';
$modle = $_REQUEST ['mod'];
$action = $_REQUEST ['action'];

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

if (! empty ( $uid )) {
	include_once PATH_VIEW_COMMAND . '/command_' . $modle . '_' . $action . '.php';
}

