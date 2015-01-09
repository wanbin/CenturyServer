<?php
$type = $_REQUEST ['type'];
$page = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
include_once PATH_HANDLER . 'ActionHandler.php';
$words = new ActionHandler ( $uid );
$ret = $words->getPage ( $page, $type );

$contenttype =$words->getTypeList();

foreach ( $ret as $key => $value ) {
	$temid=$value ['_id'];
	$temhtml = getSelectHtml ( "select_" . $value ['_id'], $contenttype, $value ['type'],"selectchange($temid)" );
	$ret[$key]['typehtml']=$temhtml;
	$ret[$key]['content']=str_replace('"', "'", stripslashes($value['content'])) ;
}




?>
