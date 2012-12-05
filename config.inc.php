<?php
$database_xmlpath = '';
$webHost = '';
$cdnHost = '';
global $config;
$config = array();
$config['dbconfig'] = array (
	'default'=>array(
		'host_num' => 1,
		'host'=>'host1',
		'db_name' => $dbHost['host1']['dbname'][0],
		'db_num' => 1,
		'table_num' => 1
	)
);

//load host config
$config['dbconfig'] = array_merge($config['dbconfig'],$dbHost);
//mapping account gameuid
$config ['cache_user_mapping'] = $cacheHost ['host1'];
$config ['cache_redis'] = $cacheHost ['redis'];

$config['cache_dispatch'] = array(
	'dispatch_num'=>1,
	'dispatch_rule'=>array(
		0=>array('min_gameuid'=>0,'max_gameuid'=>1000,'server'=>array($cacheHost['host1']['host'])),
	),
);

$config['timezone'] = 'Asia/Shanghai';
$config['database_xmlpath'] = $database_xmlpath;
$config['authcode_expire'] = 0;

$config['sns_arr'] = array(
	'default'=>array('timezone'=>'Asia/Shanghai'),
);
$config ['redis'] = false;
$config ['memcache'] = array (
		'TestContentCache' => true,
);
//逻辑开服
$config['dispatchCount'] = 10;//逻辑开服最大个数
$config['dispatchTable'] = array('user_mapping');//逻辑开服的数据表
$config['noServerCommand'] = array('GameRegister');
$config['noValidateCommand'] = array('GameInit','GameRegister','Test');
$config['webBase'] = 'http://'.$webHost.'/';
$config['cdnBase'] = 'http://'.$cdnHost.'/';
$config['version'] = array('0.0.1');
$config['version_default'] = '0.0.1';
?>