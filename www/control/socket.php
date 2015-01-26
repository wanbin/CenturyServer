<?php
include '../../define.php';
$rediska = new Rediska ();
$list = new Rediska_Key_List ( 'Socket_Map_0' );
$ret = array (
		'gameuid' => 'lIGfIF4VMwz3q2jaAAAE',
		'username' => 'lIGfIF4VMwz3q2jaAAAE',
		'message' => "我爱你" 
);
$list->append ( json_encode ( $ret ) );

?>