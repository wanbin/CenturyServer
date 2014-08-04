<?php
global $config;
$config = array();
$cacheHost=array();

$config['timezone'] = 'Asia/Shanghai';

$config ['redis'] = false;
$config ['memcache'] = array (
		'AccountCache'=>true
);


define('BAIDU_AK','v2OxzHT5dXTmzIu9pAtdhkTP');
define('BAIDU_SK','cAIVgDgquixj3nCvACyEWwdT0vjynxTe');
define('BAIDU_MYSQL_DBNAME','vGqVSTLBAJuMcqzGlevs');

define('BAIDU_MYSQL_HOST','sqld.duapp.com');
define('BAIDU_MYSQL_PORT','4050');


//JPUSH相关配置
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PWD', '');
define('DB_NAME', 'wx');
define('DB_TAB', 'zxzbcar_push');
define('DB_CODE','utf8');
define('appkeys','d0085b49b0682dbbb3a36ff5');
define('masterSecret', 'fd838eceb6aaf5276b75f542');
define('platform', 'android');
?>