<?php
include_once PATH_ROOT.'../config.host.php';
$database_xmlpath = '';
$webHost = '';
$cdnHost = '';
global $config;
$config = array();
$cacheHost=array();

$config['host_map'] = array(10000000,20000000);       //一个服务器最多用户
$config['DB_map'] = array(
			
		//服务器一
		array(
// 				'DB_host' => $dbHost['host1'],
// 				'1'=> $dbHost['host2']['dbname'][0],    //逻辑一服
// 				'2'=> $dbHost['host2']['dbname'][1]     //逻辑二服
			),
		//服务器二
// 		array(
// 				'DB_host' => $dbHost['host2'],
// 				'1'=> $dbHost['host2']['dbname'][0],    //逻辑一服
// 				'2'=> $dbHost['host2']['dbname'][1]     //逻辑二服
// 			)
	);

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
		'AccountCache'=>true
);

$config['noServerCommand'] = array('GameRegister');
$config['webBase'] = 'http://'.$webHost.'/';
$config['cdnBase'] = 'http://'.$cdnHost.'/';
$config['version'] = array('0.0.1');
$config['version_default'] = '0.0.1';


define('BAIDU_AK','v2OxzHT5dXTmzIu9pAtdhkTP');
define('BAIDU_SK','cAIVgDgquixj3nCvACyEWwdT0vjynxTe');

define('BAIDU_MYSQL_HOST','sqld.duapp.com');
define('BAIDU_MYSQL_DBNAME','vGqVSTLBAJuMcqzGlevs');
define('BAIDU_MYSQL_PORT','4050');


//JPUSH相关配置
define('DB_HOST', '42.121.123.185');
define('DB_USER', 'root');
define('DB_PWD', 'abc123');
define('DB_NAME', 'wx');
define('DB_TAB', 'zxzbcar_push');
define('DB_CODE','utf8');
define('appkeys','d0085b49b0682dbbb3a36ff5');
define('masterSecret', 'fd838eceb6aaf5276b75f542');
define('platform', 'android');
?>