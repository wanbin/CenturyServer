<?php
include '../../define.php';
$modle = $_REQUEST ['mod'];
$action = $_REQUEST ['action'];

$uid = isset ( $_REQUEST ['username'] ) ? $_REQUEST ['username']  : "";

if(!empty($uid)){
	include_once PATH_VIEW_COMMAND . '/command_' . $modle . '_' . $action . '.php';
}

