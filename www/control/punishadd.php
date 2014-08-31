<?php 
if(!empty($uid)){
	include_once PATH_HANDLER . 'PunishHandler.php';
	$punish = new PunishHandler ($uid);
	$ret=$punish->getPage(1);
	var_dump($ret);
}

?>
