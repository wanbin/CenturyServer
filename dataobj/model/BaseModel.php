<?php
require_once FRAMEWORK . 'exception/ExceptionConstants.php';
require_once 'MemcacheConstants.php';

class BaseModel {
	
	public $gameuid = NULL;
	protected $uid     = NULL;
	protected $server  = NULL;
	protected $useMemcache = null;
	protected $model = null;
	protected $commandAnalysis = null;
	protected $channel="ANDROID";
	
	/**
	 * @var Redis
	 */
	protected $redis = null;
	
	/**
	 * @var Memcache
	 */
	protected $memcache=null;
	
	//数据分析参数
	protected $selectCount = 0;
	protected $insertCount = 0;
	protected $updateCount = 0;
	protected $deleteCount = 0;
	
	protected $itemMC = null;
	protected $mysqlConnect=null;
	
	static $mongoClient=null;
	public function __construct($uid) {
		// 加载config
		$config = $GLOBALS ['config'];
		$this->useMemcache = $config ['memcache'];
		$this->model = get_class ( $this );
		
		$redis_config=ISBAIDU?$config ['redis_base_baidu']:$config ['redis_base'];
		
		$this->redis  = new Redis();
		$this->redis ->connect($redis_config['host'], $redis_config['port']);
		$this->redis->auth($redis_config['password']);
		
		
		if(ISBAIDU){
			require_once FRAMEWORK.'/sdk/BaeMemcache.class.php';
			$cacheConfig=$config ['memcache_base_baidu'];
			$this->memcache = new BaeMemcache($cacheConfig['cacheid'], $cacheConfig['host'].':'.$cacheConfig['port'], $cacheConfig['user'], $cacheConfig['password']);
		}
		else{
			$cacheConfig=$config ['memcache_base'];
			$this->memcache = new Memcache;
			$this->memcache->pconnect($cacheConfig['host'], $cacheConfig['port']);
		}
		
		
		if (isset ( $uid )&&!empty($uid)) {
			$this->uid = $uid;
			$this->gameuid = $this->getGameuid($uid);
		}
	}

	public function getGameuid($uid){
		$gameuid=$this->redis->HGET("REDIS_USER_GAMEUID",$uid);
		if($gameuid>0){
			return $gameuid;
		}
		$res = $this->getUserInfo ( $uid );
		$gameuid = $res ['_id'];
		if (empty ( $res )) {
			if (strlen ( $uid ) == strlen ( "5A74E27E8AC44C778731B7A8A8207250" )) {
				$this->channel = 'IOS';
			} elseif (substr ( $uid, 1, 5 ) == substr ( "ouHjQjpu175ug-jVh0Wdw5i--Xgw", 1, 5 )) {
				$this->channel = 'WX';
			}
			$userinfo = array (
					'_id' => $this->getIdNew ( "users" ),
					'uid' => $uid,
					'regtime' => time (),
					'channel' => $this->channel 
			);
			return $this->insertUser ( $userinfo );
		} else {
			$this->redis->HMSET ( "REDIS_USER_GAMEUID", array (
					$uid => $gameuid 
			) );
		}
		return $gameuid;
	}
	
	public function insertUser($content){
		$monogdb = $this->getMongdb ();
		$collection = $monogdb->selectCollection('users');
		$ret = $collection->insert ( $content );
		return $content['_id'];
	}
	
	public function getUserInfo($uid){
		//先从redis取
		$monogdb = $this->getMongdb ();
		$collection =  $monogdb->selectCollection('users');
		$ret = $collection->findOne ( array ('uid' => $uid ) );
		return $ret;
	}	
	
		// =================================MYSQL============================================//
		
	// 不支持同一业务的数据库水平部署在不同服务器上
	protected function getDBInstance($tableName) {
		static $DBInstances = array ();
		if (isset ( $DBInstances [$tableName] )) {
			return $DBInstances [$tableName];
		}
		$DBHandler = new DBHandler ( $tableName, $this->gameuid, $this->server );
		$DBInstances [$tableName] = $DBHandler;
		return $DBHandler;
	}
	
	
	public function oneSqlSignle($sql) {
		$ret=$this->oneSql($sql);
		return $ret[0];
	}
	
	public function oneSql($sql) {
		
		$this->baidudebug($sql);
		$DBHandler = $this->getDBInstance ( $this->getTableName () );
		$tem = explode ( ' ', $sql );
		if (in_array ( $tem [0], array (
				'insert',
				'update',
				'replace',
				'delete'
		) )) {
			$sqlarr=explode(';', $sql);
			foreach ($sqlarr as $key=>$value){
				 $this->BaiduExecute ( $value );
			}
			return;
		} else {
			return $this->BaiduContent($sql);
		}
	}
	
	protected function BaiduContent($sql) {
		$host = DB_HOST;
		$user = DB_USER;
		$pwd = DB_PWD;
		$port=DB_PORT;
		$dbname=DB_NAME;
		if (ISBAIDU == 1) {
			$host = BAIDU_MYSQL_HOST;
			$port = BAIDU_MYSQL_PORT;
			$user = BAIDU_AK;
			$pwd = BAIDU_SK;
			$dbname = BAIDU_MYSQL_DBNAME;
		}
		//$link = mysql_connect ( );
		$link = mysql_connect ( "localhost:3306", 'root', '', true );
		if (! $link) {
			die ( "Connect Server Failed: " . mysql_error () );
		}
		if (! mysql_select_db ( $dbname, $link )) {
			die ( "Select Database Failed: " . mysql_error ( $link ) );
		}
		$ret = mysql_query ( $sql, $link );
		if (! $ret) {
			$this->writeSqlError ( $sql, mysql_error ( $link ) );
			return array();
		}
		$result=array();
		while ($row = mysql_fetch_assoc($ret)) {
			$result[]=$row;
		}
		return $result;
	}
	protected function BaiduExecute($sql) {
		if (empty ( $sql )) {
			return false;
		}
		
		$host = BAIDU_MYSQL_HOST;
		$port = BAIDU_MYSQL_PORT;
		$user = BAIDU_AK;
		$pwd = BAIDU_SK;
		$dbname = BAIDU_MYSQL_DBNAME;
		
		
		
		$link = @mysql_connect ( "{$host}:{$port}", $user, $pwd, true );
		if (! $link) {
			die ( "Connect Server Failed: " . mysql_error () );
		}
		if (! mysql_select_db ( $dbname, $link )) {
			die ( "Select Database Failed: " . mysql_error ( $link ) );
		}
		$ret = mysql_query ( $sql, $link );
		if (! $ret) {
			$this->writeSqlError ( $sql, mysql_error ( $link ) );
			return false;
		}
		return true;
	}
	
	protected function getTableName() {
		return '';
	}
	
	/**
	 +----------------------------------------------------------
	 * 连接值的字符串
	 +----------------------------------------------------------
	 * @param array $values
	 * @return string
	 +----------------------------------------------------------
	 */
	private function joinValuesStr($values)
	{
		foreach ( $values as $key => $val )
		{
			$str .= $key . "='" . $val . "',";
		}
		$str = rtrim ( $str, ',' );
		return $str;
	}
	
	
	public function writeSqlError($sql, $e) {
		if (ISBAIDU==1) {
			require_once FRAMEWORK."/BaeLog.class.php";
			$user = BAIDU_AK;
			$pwd = BAIDU_SK;
			$logger=BaeLog::getInstance(array('user'=>$user, 'passwd'=> $pwd));
			$logger->setLogLevel(16);
			$logger->setLogTag("sql_error");
			$logger->Fatal($e);
		}else{
			$fileName = date ( "Y-m-d", time () ) . "sqlerror.sql";
			$temtime = date ( "Y-m-d H:i:s", time () );
			$strAdd = "#[$temtime]\n";
			file_put_contents ( PATH_ROOT . "/log/$fileName", $strAdd . $e . $sql, FILE_APPEND );
		}
	}
	
	public function baidudebug($message){
		if (ISBAIDU==1) {
			require_once FRAMEWORK."/BaeLog.class.php";
			$user = BAIDU_AK;
			$pwd = BAIDU_SK;
			$logger=BaeLog::getInstance(array('user'=>$user, 'passwd'=> $pwd));
			$logger->setLogLevel(16);
			$logger->setLogTag("sql_query");
			$logger->Debug($message);
		}
	}
	
	//=========================================Cache=====================================//

	/**
	 * 返回需要操作的缓存实例
	 *
	 * @param $config_key string
	 *       	 配置缓存的key
	 * @return Cache
	 */
	protected function getCacheInstance($config_key = '') {
		static $instances = array ();
		if (isset ( $instances [$config_key] )) {
			return $instances [$config_key];
		}
		$config = $GLOBALS ['config'] [$config_key];
		if (! isset ( $config ['host'] )) {
			$this->throwException ( 'cache config error', StatusCode::CONFIG_ERROR );
		}
		$cache = new Cache ( $config ['host'] );
		$instances [$config_key] = $cache;
		return $cache;
	}
	
	/**
	 * 返回需要操作的缓存实例
	 *
	 * @param $gameuid int
	 * @return Cache
	 */
	protected function getCacheInstanceNoHash($gameuid) {
		static $cacheinstances = array ();
		if (empty ( $gameuid )) {
			$gameuid = $this->gameuid;
		}
		$config = $this->getConfig ( 'cache_dispatch' );
		$server_index = $this->getCacheServerIndex ( $gameuid );
		if (isset ( $cacheinstances [$server_index] )) {
			return $cacheinstances [$server_index];
		}
		$cache = new Cache ( $config ['dispatch_rule'] [$server_index] ['server'] );
		$cacheinstances [$server_index] = $cache;
		return $cache;
	}
	/**
	 * 打印调试信息
	 *
	 * @param $msg string
	 *       	 消息
	 * @param $var mixed
	 *       	 附加的变量值
	 */
	protected function debug($msg, $var = null) {
		if (DEBUG) {
			$file = $GLOBALS ['config'] ['log_path'] . 'debug_logs/model_debug_' . date ( 'Y-m-d' ) . '.log';
			if (! file_exists ( $GLOBALS ['config'] ['log_path'] . 'debug_logs' )) {
				mkdir ( $GLOBALS ['config'] ['log_path'] . 'debug_logs', 0777, true );
			}
			$m = '[' . date ( 'Y-m-d H:i:s' ) . '] ' . $msg;
			if (isset ( $var )) {
				$m .= print_r ( $var, true );
			}
			try {
				file_put_contents ( $file, $m . "\n", FILE_APPEND );
			} catch ( Exception $e ) {
			}
		}
	}
	
	protected function getIdNew($idname) {
		return $this->redis->HINCRBY( "REDIS_KEY_ADD_ID",$idname,1);
	}
	protected function getMongdb() {
		if(ISBAIDU){
			$host=BAIDU_MONGO_HOST;
			$port=BAIDU_MONGO_PORT;
			$dbname=BAIDU_MONGO_DBNAME;
			$user = BAIDU_AK;
			$pwd = BAIDU_SK;
			if($this->mongoClient==null){
				$this->mongoClient = new MongoClient("mongodb://{$host}:{$port}");
			}
			$mongoDB = $this->mongoClient->selectDB($dbname);
			$mongoDB->authenticate($user, $pwd);
			return $mongoDB;
		}
		else{
			if($this->mongoClient==null){
				$this->mongoClient = new MongoClient("mongodb://localhost:27017");
			}
			$mongoDB = $this->mongoClient->selectDB('centurywar');
			return $mongoDB;
		}
	}
}
