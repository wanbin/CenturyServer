<?php
if(!ISBAIDU){
	session_start ();
}
include '../../define.php';
$modle = $_REQUEST ['mod'];
$action = $_REQUEST ['action'];

$uid = isset ( $_SESSION ['username'] ) ? $_SESSION ['username'] : "";
if(empty($uid)){
	include_once PATH_VIEW_COMMAND . '/command_' . $modle . '_' . $action . '.php';
}

