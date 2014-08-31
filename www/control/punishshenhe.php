<?php

include_once PATH_HANDLER . 'PunishHandler.php';
$punish = new PunishHandler ( $uid );
$ret = $punish->getPageShenHe ( 1 );
?>
