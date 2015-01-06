<?php 
$page=isset($_REQUEST['page'])?$_REQUEST['page']:1;
// echo $page;
include_once PATH_HANDLER . 'PunishHandler.php';
$punish = new PunishHandler ( $uid );
$ret = $punish->getPageList ( $page,0 );

?>
