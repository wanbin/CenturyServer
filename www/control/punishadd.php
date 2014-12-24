<?php 
$punishid=$_REQUEST['punishid'];
if(!empty($punishid)){
	include_once PATH_HANDLER . 'PunishHandler.php';
	$punish = new PunishHandler ($uid,'WEB');
	$ret=$punish->getPunish($punishid);
//  	var_dump($ret);
}

?>
