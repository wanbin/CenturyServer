<?php
include_once '../../Entry.php';
include_once '../amf/services/BaseService.php';
$s = new BaseService();
$params=json_decode(str_replace('\\"', '"', $_REQUEST['params']),true);
if (empty ( $params )) {
	echo json_encode(array('params is empty!'));
} else {
	echo json_encode( $s->dispatch ( $params ) );
}
?>