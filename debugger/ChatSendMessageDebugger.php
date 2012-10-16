<?php
include_once '../www/amf/services/BaseService.php';
include_once './config.inc.php';

$s = new BaseService();
$params = array(
	'data'=>array(
		array(
			'cmd' => 'ChatSendMessage',
			'params' => array(
						'content'=>"What are you 6",
						'receiver' => '82003'	
					)		
		)
	),
	'code' => $AuthCode,
	'guid' => $gameuid,
	'uid' => $uid,
	'pid' => $pid,
	'server' =>1
);
echo "<pre>\r\n";
print_r($s->dispatch($params));