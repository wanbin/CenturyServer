<?php
require_once FRAMEWORK . 'exception/ExceptionConstants.php';
require_once 'MemcacheConstants.php';
class DBModel {
	/**
	 *
	 * @var Redis
	 */
	protected static $redis = null;
	
	/**
	 *
	 * @var Memcache
	 */
	protected static $memcache = null;
	protected static $redisKa = null;
	
	// 数据分析参数
	protected $selectCount = 0;
	protected $insertCount = 0;
	protected $updateCount = 0;
	protected $deleteCount = 0;
	protected $mysqlConnect = null;
	protected static $mongoClient = array ();
	protected static $mongoConnectionPool = array ();
	public function __construct() {
		$this->model = get_class ( $this );
		
		global $memcache, $redisKa;
		if ($memcache == null) {
			$memcache = new Memcache ();
			$memcache->pconnect ( MEMCACHE_HOST, MEMCACHE_PORT );
		}
		
		if ($redisKa == null) {
			$redisKa = new Rediska ( array (
					'namespace' => REDIS_NAMESPACE_NAME,
					'servers' => array (
							array (
									'host' => REDIS_HOST,
									'port' => REDIS_PORT,
									'alias' => REDIS_ALIAS 
							) 
					) 
			) );
		}
	}
	
	// 基本cache操作
	protected function setToCache($key, $value, $expird = 0) {
		global $memcache;
		return $memcache->set ( $key, $value, false, $expird );
	}
	protected function getFromCache($key) {
		global $memcache;
		return $memcache->get ( $key );
	}
	protected function delFromCache($key) {
		global $memcache;
		return $memcache->delete ( $key );
	}
	
	/////////////////////////MONGODB/////////////////////
	
	// 这块mongodb 太逆天啦，我单独处理一下试试
	protected function insertMongo($content, $collectionName, $dbname = 'centurywar') {
		if (! isset ( $content ['_id'] )) {
			$content ['_id'] = $this->getIdNew ( $collectionName );
		}
		if (! isset ( $content ['time'] )) {
			$content ['time'] = time ();
		}
		try {
			$mongoCollection = $this->getMongoConnection ( $dbname, $collectionName );
			$ret = $mongoCollection->insert ( $content );
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		return $content ['_id'];
	}
	
	/**
	 * 更新mongo内容
	 *
	 * @param
	 *        	unknown_type array('nickname'=>'wanbin')
	 * @param
	 *        	unknown_type array('roomid'=>intval ( $id ))
	 * @param unknown_type $dbname        	
	 * @return boolean
	 */
	protected function updateMongo($content, $where, $collectionName, $dbname = 'centurywar', $inc = array()) {
		try {
			$mongoCollection = $this->getMongoConnection ( $dbname, $collectionName );
			if (! empty ( $inc )) {
				$result = $mongoCollection->update ( $where, array (
						'$set' => $content,
						'$inc' => $inc 
				) );
			} else {
				$result = $mongoCollection->update ( $where, array (
						'$set' => $content 
				) );
			}
			return true;
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	}
	protected function removeMongo($where, $collectionName, $dbname = 'centurywar') {
		try {
			$mongoCollection = $this->getMongoConnection ( $dbname, $collectionName );
			$result = $mongoCollection->remove ( $where );
			return true;
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		return;
	}
	protected function getFromMongo($where, $collectionName, $sort = array('_id'=>-1), $skip = 0, $limit = 100, $dbname = 'centurywar') {
		$ret = array ();
		try {
			$mongoCollection = $this->getMongoConnection ( $dbname, $collectionName );
			$mongoCursor = $mongoCollection->find ( $where )->sort ( $sort )->skip ( $skip )->limit ( $limit );
			while ( $mongoCursor->hasNext () ) {
				$ret [] = $mongoCursor->getNext ();
			}
			return $ret;
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		return array ();
	}
	protected function getMongoCount($where, $collectionName, $dbname = 'centurywar') {
		try {
			$mongoCollection = $this->getMongoConnection ( $dbname, $collectionName );
			$count = $mongoCollection->find ( $where )->count ();
			return $count;
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		return 0;
	}
	protected function getOneFromMongo($where, $collectionName, $dbname = 'centurywar') {
		$ret = array ();
		try {
			$mongoCollection = $this->getMongoConnection ( $dbname, $collectionName );
			$mongoCursor = $mongoCollection->find ( $where )->limit ( 1 );
			while ( $mongoCursor->hasNext () ) {
				$ret [] = $mongoCursor->getNext ();
			}
			return $ret [0];
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		return;
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
		$ret = $this->oneSql ( $sql );
		return $ret [0];
	}
	public function oneSql($sql) {
		$this->baidudebug ( $sql );
		$DBHandler = $this->getDBInstance ( $this->getTableName () );
		$tem = explode ( ' ', $sql );
		if (in_array ( $tem [0], array (
				'insert',
				'update',
				'replace',
				'delete' 
		) )) {
			$sqlarr = explode ( ';', $sql );
			foreach ( $sqlarr as $key => $value ) {
				$this->BaiduExecute ( $value );
			}
			return;
		} else {
			return $this->BaiduContent ( $sql );
		}
	}
	protected function getTableName() {
		return '';
	}
	
	/**
	 * +----------------------------------------------------------
	 * 连接值的字符串
	 * +----------------------------------------------------------
	 *
	 * @param array $values        	
	 * @return string
	 *         +----------------------------------------------------------
	 */
	private function joinValuesStr($values) {
		foreach ( $values as $key => $val ) {
			$str .= $key . "='" . $val . "',";
		}
		$str = rtrim ( $str, ',' );
		return $str;
	}
	public function writeSqlError($sql, $e) {
		if (ISBAIDU) {
			require_once FRAMEWORK . "/BaeLog.class.php";
			$user = BAIDU_AK;
			$pwd = BAIDU_SK;
			$logger = BaeLog::getInstance ( array (
					'user' => $user,
					'passwd' => $pwd 
			) );
			$logger->setLogLevel ( 16 );
			$logger->setLogTag ( "sql_error" );
			$logger->Fatal ( $e );
		} else {
			$fileName = date ( "Y-m-d", time () ) . "sqlerror.sql";
			$temtime = date ( "Y-m-d H:i:s", time () );
			$strAdd = "#[$temtime]\n";
			file_put_contents ( PATH_ROOT . "/log/$fileName", $strAdd . $e . $sql, FILE_APPEND );
		}
	}
	
	// =========================================Cache=====================================//
	
	/**
	 * 打印调试信息
	 *
	 * @param $msg string
	 *        	消息
	 * @param $var mixed
	 *        	附加的变量值
	 */
	protected function debug($msg, $var = null) {
		if (DEBUG) {
			$file = PATH_LOG . '/model_debug_' . date ( 'Y-m-d' ) . '.log';
			$m = '[' . date ( 'Y-m-d H:i:s' ) . '] ' . $msg . "\n";
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
		$list=new Rediska_Key_Hash(MONGO_DB_NAME."_REDIS_KEY_ADD_ID");
		$ret= $list->increment($idname);
		if ($ret < 100000) {
			$ret += 100000;
			$list->set ( $list->set ( $idname, $ret ) );
		}
		return $ret;
	}
	protected function getMongdb($dbname) {
		global $mongoClient;
		if ($mongoClient [$dbname] == null) {
				if(MONGO_CLIENT){
				$mongostr="mongodb://".MONGO_DB_HOST.":".MONGO_DB_PORT.",".MONGO_DB_HOST2.":".MONGO_DB_PORT2.",".MONGO_DB_HOST3.":".MONGO_DB_PORT3;
				$mongoClient = new MongoClient ($mongostr,array('replicaSet'=>'sdsell'));
			}else{
				$mongostr="mongodb://".MONGO_DB_HOST.":".MONGO_DB_PORT;
				$mongoClient = new MongoClient ($mongostr);
			}
		}
		$mongoDb = $mongoClient [$dbname]->selectDB ( $dbname );
		return $mongoDb;
	}
	protected function getMongoConnection($dbname, $table) {
		if (! isset ( $this->mongoConnectionPool [$dbname] [$table] )) {
			$mongoDB = $this->getMongdb ( $dbname );
			$this->mongoConnectionPool [$dbname] [$table] = $mongoDB->selectCollection ( $table );
		}
		return $this->mongoConnectionPool [$dbname] [$table];
	}
	public function getTimeStr($time) {
		if (time () - $time < 60) {
			return "刚刚";
		}
		if (time () - $time < 600) {
			return "几分钟前";
		}
		if (time () - $time < 3600) {
			return "一小时前";
		}
		return date ( "Y-m-d", $time );
	}
}
