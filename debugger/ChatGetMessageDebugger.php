<?php
include_once '../www/amf/services/BaseService.php';
include_once './config.inc.php';

$s = new BaseService();
$params = array(
	'data'=>array(
		array(
			'cmd' => 'ChatGetMessage',
			'params' => array(
				'receiver'=>'82003'
			)
		)
	),
	'guid' => $gameuid,
	'uid' => $uid,
	'pid' => $pid,
	'code' => $AuthCode,
	'server' =>1
);


echo "<pre>\r\n";
print_r($s->dispatch($params));