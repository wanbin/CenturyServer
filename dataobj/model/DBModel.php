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
		if ($this->redis == null) {
			$this->redis = new Redis ();
			$this->redis->connect ( REDIS_HOST, REDIS_PORT );
			// $this->redis->auth ( $redis_config ['password'] );
		}
		
		if ($this->memcache == null) {
			$this->memcache = new Memcache ();
			$this->memcache->pconnect ( MEMCACHE_HOST, MEMCACHE_PORT );
		}
		
		if ($this->redisKa == null) {
			$this->redisKa = new Rediska ( array (
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
		return $this->memcache->set ( $key, $value, false, $expird );
	}
	protected function getFromCache($key) {
		return $this->memcache->get ( $key );
	}
	protected function delFromCache($key) {
		return $this->memcache->delete ( $key );
	}
	
	// 基本redis操作
	protected function setRedisHash($key, $field, $value) {
		return $this->redis->HMSET ( $key, array (
				$field => $value 
		) );
	}
	protected function getRedisHash($key, $field) {
		return $this->redis->HGET ( $key, $field );
	}
	protected function getRedisHashAll($key) {
		return $this->redis->HGETALL ( $key );
	}
	protected function incrList($key, $field) {
		return $this->redis->HINCRBY ( $key, $field, 1 );
	}
	protected function pushList($key, $field) {
		return $this->redis->RPUSH ( $key, $field );
	}
	protected function pushListLeft($key, $field) {
		return $this->redis->LPUSH ( $key, $field );
	}
	protected function getListAll($key) {
		return $this->redis->LRANGE ( $key, 0, $this->redis->LLEN ( $key ) );
	}
	protected function getListRange($key, $start, $end) {
		return $this->redis->LRANGE ( $key, $start, $end );
	}
	protected function getListLen($key) {
		return $this->redis->LLEN ( $key );
	}
	protected function getHashLen($key) {
		return $this->redis->HLEN ( $key );
	}
	protected function removeList($key, $value) {
		return $this->redis->LREM ( $key, $value );
	}
	protected function removeHash($key, $value) {
		return $this->redis->HDEL ( $key, $value );
	}
	protected function getListValueByIndex($key, $index) {
		return $this->redis->LINDEX ( $key, $index );
	}
	protected function delRedis($key) {
		return $this->redis->DEL ( $key );
	}
	protected function isExit($key, $value) {
		return $this->redis->HEXISTS ( $key, $value );
	}
	
	// 有序集合
	protected function sortAdd($key, $souce, $member) {
		return $this->redis->ZADD ( $key, $souce, $member );
	}
	protected function incrSortOne($key, $souce, $member) {
		return $this->redis->ZINCRBY ( $key, $souce, $member );
	}
	protected function getSortRankLowToHigh($key, $member) {
		return $this->redis->ZREVRANK ( $key, $member );
	}
	protected function getSortRank($key, $member) {
		return $this->redis->ZRANK ( $key, $member );
	}
	protected function getSortValue($key, $member) {
		return $this->redis->ZSCORE ( $key, $member );
	}
	protected function getRankString($key, $start, $end) {
		return $this->redis->ZRANGE ( $key, $start, $end, true );
	}
	protected function getRankStringRev($key, $start, $end) {
		return $this->redis->ZREVRANGE ( $key, $start, $end, true );
	}
	
	// /////////////////////////MONGODB/////////////////////
	
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
		return $this->redis->HINCRBY ( "REDIS_KEY_ADD_ID", $idname, 1 );
	}
	protected function getMongdb($dbname) {
		global $mongoClient;
		if ($mongoClient [$dbname] == null) {
			$mongostr="mongodb://".MONGO_DB_HOST.":".MONGO_DB_PORT.",".MONGO_DB_HOST2.":".MONGO_DB_PORT2.",".MONGO_DB_HOST3.":".MONGO_DB_PORT3;
			$mongoClient = new MongoClient ($mongostr,array('replicaSet'=>'sdsell'));
		}
		$mongoDb = $mongoClient[$dbname]->selectDB ( $dbname );
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
