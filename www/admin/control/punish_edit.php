<?php
$type = $_REQUEST ['type'];
$page = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
include_once PATH_HANDLER . 'PunishHandler.php';
$punish = new PunishHandler ( $uid );
if ($type == 0 || $type == 2) {
	$ret = $punish->getPageShenHe ( $page, $type );
} else {
	$ret = $punish->getPageList ( $page, $type % 10 );
}
$contenttype = array (
		array (
				"value" => 1,
				'content' => '真心话' 
		),
		array (
				"value" => 2,
				'content' => '大冒险' 
		),
		array (
				"value" => 3,
				'content' => '看演技' 
		) 
);

foreach ( $ret as $key => $value ) {
	$temid=$value ['_id'];
	$temhtml = getSelectHtml ( "select_" . $value ['_id'], $contenttype, $value ['contenttype'],"selectchange($temid)" );
	$ret[$key]['typehtml']=$temhtml;
}




?>
