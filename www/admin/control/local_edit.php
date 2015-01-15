<?php
$type = isset($_REQUEST ['type'])?$_REQUEST ['type']:0;
$page = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
include_once PATH_HANDLER . 'LocalHandler.php';
$words = new LocalHandler ( $uid );
$ret = $words->getPage (  $type );

$contenttype =$words->getTypeList();

foreach ( $ret as $key => $value ) {
	$temid=$value ['_id'];
	$temhtml = getSelectHtml ( "select_" . $value ['_id'], $contenttype, $value ['type'],"" );
	$ret[$key]['typehtml']=$temhtml;
	$ret[$key]['content']=str_replace('"', "'", stripslashes($value['content'])) ;
}

$list=$words->getTypeList();

?>
