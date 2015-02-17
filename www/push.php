<?php
include_once '../define.php';
$rediska = new Rediska ( array (
		'servers' => array (
				array (
						'host' => REDIS_HOST,
						'port' => REDIS_PORT,
						'alias' => REDIS_ALIAS 
				) 
		) 
) );
$list = new Rediska_Key_List('Socket_Map_0');
$date=array('message'=>'dddd','channel'=>'android','alias'=>"A0000043A574DC");

$list[]=json_encode($date);
$list[]=json_encode($date);
$list[]=json_encode($date);
$list[]=json_encode($date);