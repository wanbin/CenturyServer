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

define('BAIDU_REDIS_HOST','redis.duapp.com');
define('BAIDU_REDIS_PORT','80');
define('BAIDU_REDIS_DBNAME','esdImygxeYaDGMxKXqtU');


define('BAIDU_MONGO_HOST','mongo.duapp.com');
define('BAIDU_MONGO_PORT','8908');
define('BAIDU_MONGO_DBNAME','KGqspGheASthPzbuQmcE');


define('BAIDU_CACHE_HOST','cache.duapp.com');
define('BAIDU_CACHE_PORT','20243');
define('BAIDU_CACHE_DBNAME','IxQLDAmTlrkMPFzuTnJW');


//JPUSH相关配置
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PWD', '');
define('DB_NAME', 'wx');
define('DB_TAB', 'zxzbcar_push');
define('DB_CODE','utf8');
define('appkeys','d0085b49b0682dbbb3a36ff5');
define('masterSecret', 'fd838eceb6aaf5276b75f542');
define('platform', 'android');


$config ['redis_base_baidu'] = array ('host' => BAIDU_REDIS_HOST, 'port' => BAIDU_REDIS_PORT, 'password' => BAIDU_AK . "-" . BAIDU_SK . "-" . BAIDU_REDIS_DBNAME );
$config ['redis_base'] = array ('host' => 'localhost', 'port' => 6379, 'password' => '' );	


$config['memcache_base_baidu']=array('host' => BAIDU_CACHE_HOST, 'port' => BAIDU_CACHE_PORT,'user'=>BAIDU_AK, 'password' => BAIDU_SK,'cacheid'=>BAIDU_CACHE_DBNAME);
$config['memcache_base']=array('host' => '127.0.0.1', 'port' => 12121, 'password' => '','cacheid'=>'');

define("SHARE",'<div class="bdsharebuttonbox pull-right"><a href="#" class="bds_more" data-cmd="more"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a></div>
<script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"24"},"share":{},"image":{"viewList":["tsina","qzone","tqq","renren","weixin"],"viewText":"分享到：","viewSize":"16"},"selectShare":{"bdContainerClass":null,"bdSelectMiniList":["tsina","qzone","tqq","renren","weixin"]}};with(document)0[(getElementsByTagName("head")[0]||body).appendChild(createElement("script")).src="http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion="+~(-new Date()/36e5)];</script>')


?>