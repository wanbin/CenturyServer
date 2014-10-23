<?php
$page=isset($_REQUEST['page'])?$_REQUEST['page']:0;
include_once PATH_HANDLER . 'PunishHandler.php';
$punish = new PunishHandler ( $uid );
$ret = $punish->getPageShenHe ($page);

?>
