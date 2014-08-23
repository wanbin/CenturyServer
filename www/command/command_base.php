<?php
session_start ();
include '../../define.php';
$modle = $_REQUEST ['mod'];
$action = $_REQUEST ['action'];
include_once FRAMEWORK . '/utility/TimeUtil.class.php';
$timer = new TimeUtil ();
$timer->start ();

$uid = isset ( $_SESSION ['username'] ) ? $_SESSION ['username'] : "";


if (DEBUG) {
	//print_R ( $_REQUEST );
}
if(!empty($uid)){
include_once PATH_VIEW_COMMAND . '/command_' . $modle . '_' . $action . '.php';
}

$timer->stop ();
$usertime = $timer->spent ();

if (DEBUG) {
	//echo "<div>TimeUse:$usertime</div>";
} 