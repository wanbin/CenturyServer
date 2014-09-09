<?php
session_start ();
include '../../define.php';
$modle = $_REQUEST ['mod'];
$action = $_REQUEST ['action'];

if (isset ( $_SESSION ['username'] )) {
	$uid = $_SESSION ['username'];
} else {
	$uid = isset ( $_REQUEST ['username'] ) ? $_REQUEST ['username'] : "";
	if (! empty ( $uid )) {
		$_SESSION ['username'] = $uid;
	}
}


if(!empty($uid)){
	include_once PATH_VIEW_COMMAND . '/command_' . $modle . '_' . $action . '.php';
}

