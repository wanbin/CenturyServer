<?php
require_once PATH_DATAOBJ . 'SystemConstants.php';
require_once PATH_DATAOBJ . 'Constants.php';

class BaseModel {
	const MODIFY_FLAG = 'm';
	const REQUEST_COUNT = 'rc';
	
	protected $gameuid = null;
	protected $uid = null;
	protected $dbconfig = null;
	protected $sns_id = null;
	
	// hash后的库名
	protected $db_name = null;
	// hash后的表名
	protected $table_name = null;
	protected $server = null;
	// 数据库服务器
	protected $host = null;
	protected $useHandlerSocket = null;
	protected $useMemcache = null;
	protected $model = null;
	protected $dispatchTable = null;
	protected $rediska = null;
	protected $useRedis = null;
	protected $commandAnalysis = null;
	
	//数据分析参数
	protected $selectCount = 0;
	protected $insertCount = 0;
	protected $updateCount = 0;
	protected $delCount = 0;
	
	protected $itemMC = null;
	public function __construct($gameuid = null, $uid = null) {
		// 加载config
		$config = $GLOBALS ['config'];
		$this->dbconfig = $config ['dbconfig'];
		$this->gameuid = intval ( $gameuid );
		$this->uid = trim ( $uid );
		$this->useHandlerSocket = $config ['handlersocket'];
		$this->useMemcache = $config ['memcache'];
		$this->model = get_class ( $this );
		$this->dispatchTable = $config ['dispatchTable'];
		$this->useRedis = $config ['redis'];
		// $this->commandAnalysis = $config ['command_analysis'];
	}
	protected function createItemMC() {
		if (empty ( $this->itemMC )) {
			//include_once PATH_CACHE . 'ItemCache.php';
			//$itemMC = new ItemCache ( $this->gameuid );
			//$this->itemMC = $itemMC;
		}
		return $this->itemMC;
	}
	/**
	 * 获取代码的版本
	 * 
	 * @return string
	 */
	public function getVersion() {
		return GAME_VERSION;
	}
	/**
	 * 取到逻辑服
	 * @param unknown_type $server
	 */
	public function setServer($server) {
		$this->server = $server;
	}
	
	// 初始化redis 代码
	public function createRedis($redisHost = 'redis') {
		if ($this->useRedis) {
			if (isset ( $GLOBALS ['cacheHost'] [$redisHost] )) {
				$hostArr = explode ( ':', $GLOBALS ['cacheHost'] [$redisHost] ['host'] );
				$servers = array (array ('host' => $hostArr [0], 'port' => $hostArr [1] ) );
				unset ( $this->rediska );
				$this->rediska = new Rediska ();
				$this->rediska->setServers ( $servers );
			} else {
				$this->rediska = new Rediska ();
			}
		}
	}
	
	/**
	 * 设置数据库配置
	 *
	 * @param $host string
	 *       	 数据库主机
	 * @param $dbname string
	 *       	 数据库名
	 * @param $user string
	 *       	 数据库访问用户名
	 * @param $password string
	 *       	 用户密码
	 * @param $pconnect string
	 *       	 是否常连接
	 * @param $encoding string
	 *       	 使用的编码
	 * @param $dbprefix string
	 *       	 数据库的前缀
	 */
	protected function setDBConfig() {
		return array ('host' => $this->host, 
				'dbname' => $this->db_name,
				'username' => $this->user, 
				'password' => $this->password, 
				'pconnect' => false, 
				'encoding' => 'UTF8', 
				'dbprefix' => '' );
	}
	
	/**
	 * 设置用户的gameuid和uid
	 *
	 * @param $gameuid int       	
	 * @param $uid string       	
	 */
	public function setGameuid($gameuid, $uid = null) {
		$this->gameuid = $gameuid;
		$this->uid = $uid;
	}
	
	protected function closeConnection() {
		if ($this->dbhelper) {
			$this->dbhelper->close ();
			$this->dbhelper = null;
		}
	}
	
	// 不支持同一业务的数据库水平部署在不同服务器上
	protected function getMysqlInstance($table_name, $config_key, $gameuid) {
		static $mysqlinstances = array ();
		if (empty ( $gameuid )) {
			$gameuid = $this->gameuid;
		}
		$this->getDbInfo ( $table_name, $gameuid );
		if (isset ( $mysqlinstances [$config_key] )) {
			return $mysqlinstances [$config_key];
		}
		$mysqlconfig = $this->setDBConfig ();
		$dbhelper = new DBHelper2 ( $mysqlconfig );
		$mysqlinstances [$config_key] = $dbhelper;
		return $dbhelper;
	}
	
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
	 * 得到当前gameuid的缓存服务器配置
	 * 
	 * @param $gameuid int       	
	 * @return int $server_index
	 */
	protected function getCacheServerIndex($gameuid) {
		if (empty ( $gameuid )) {
			$gameuid = $this->gameuid;
		}
		$config = $this->getConfig ( 'cache_dispatch' );
		$server_num = $config ['dispatch_num'];
		$server_index = 0;
		
		if (count ( $config ['dispatch_rule'] ) != $server_num) {
			$this->throwException ( 'max cache server is not set', StatusCode::CONFIG_ERROR );
		}
		
		if ($gameuid >= $config ['dispatch_rule'] [$server_num - 1] ['min_gameuid']) {
			$server_index = $server_num - 1;
		} else {
			foreach ( $config ['dispatch_rule'] as $key => $val ) {
				$min_limits = $val ['min_gameuid'];
				$max_limits = $val ['max_gameuid'];
				if ($gameuid >= $min_limits && $gameuid <= $max_limits) {
					$server_index = $key;
				}
			}
		}
		return $server_index;
	}
	
	/**
	 * 分缓存块，取多个gameuid不同但前缀相同的key
	 * 
	 * @param $keys 需要get的key的数组       	
	 * @param $prefix 缓存key的前缀       	
	 */
	protected function mulitGetFromCache($keys, $prefix) {
		$config = $this->getConfig ( 'cache_dispatch' );
		$keys_grouping = array ();
		foreach ( $keys as $cache_key ) {
			$gameuid = substr ( $cache_key, strlen ( $prefix ) );
			$server_index = $this->getCacheServerIndex ( $gameuid );
			$keys_grouping [$server_index] [] = $cache_key;
		}
		$list = array ();
		foreach ( $config ['dispatch_rule'] as $key => $val ) {
			if (is_array ( $keys_grouping [$key] )) {
				$cache = new Cache ( $val ['server'] );
				$thisList = $cache->get ( $keys_grouping [$key] );
				if (! isset ( $thisList )) {
					$this->writeLog ( $key );
				}
				$list = array_merge ( $list, $thisList );
			}
		}
		return $list;
	}
	
		/**
	 * 从缓存中根据key取得相应的值
	 *
	 * @param $key string       	
	 * @return mixed
	 */
	protected function getFromCache($key, $gameuid) {
		if (isset ( $this->useMemcache [$this->model] ) && $this->useMemcache [$this->model]) {
			if (empty ( $gameuid )) {
				$gameuid = $this->gameuid;
			}
			$cache = $this->getCacheInstanceNoHash ( $gameuid );
			$ret = $cache->get ( $key );
		} else {
			return false;
		}
	}
	/**
	 * 记录没有命中的缓存键
	 * @param unknown_type $key
	 */
	private function writeErrorLog($key) {
		file_put_contents ( PATH_ROOT . '/memcache_notGet.log', '[' . date ( 'Y-M-D H:i:s', time () ) . ']' . $key . "\n", FILE_APPEND );
	}
	
	protected function decrementToCache($key, $value, $expire = 600, $gameuid) {
		if ($this->useMemcache [$this->model]) {
			if (empty ( $gameuid )) {
				$gameuid = $this->gameuid;
			}
			$cache = $this->getCacheInstanceNoHash ( $gameuid );
			$re = $cache->decrement ( $key, $value, $expire );
			if ($re === false) {
				if (DEBUG) {
					$this->throwException ( 'decrement cache of key ' . $key . ' failed', StatusCode::SET_MEMCACHE_ERROR );
				} else {
					$this->throwException ( 'decrement to memcache failed', StatusCode::SET_MEMCACHE_ERROR );
				}
			}
		}
	}
	protected function incrementToCache($key, $value, $expire = 600, $gameuid) {
		if ($this->useMemcache [$this->model]) {
			if (empty ( $gameuid )) {
				$gameuid = $this->gameuid;
			}
			$cache = $this->getCacheInstanceNoHash ( $gameuid );
			$re = $cache->increment ( $key, $value, $expire );
			if ($re === false) {
				if (DEBUG) {
					$this->throwException ( 'increment cache of key ' . $key . ' failed', StatusCode::SET_MEMCACHE_ERROR );
				} else {
					$this->throwException ( 'increment to memcache failed', StatusCode::SET_MEMCACHE_ERROR );
				}
			}
		}
	}
	/**
	 * 将一个值设置到缓存
	 *
	 * @param $key string
	 *       	 保存的键值
	 * @param $value mixed
	 *       	 要保存的值
	 * @param $expire int
	 *       	 过期时间
	 */
	protected function setToCache($key, $value, $expire = 600, $gameuid) {
		if ($this->useMemcache [$this->model]) {
			if (empty ( $gameuid )) {
				$gameuid = $this->gameuid;
			}
			$cache = $this->getCacheInstanceNoHash ( $gameuid );
			$re = $cache->set ( $key, $value, $expire, MEMCACHE_COMPRESSED );
			if ($re === false) {
				if (DEBUG) {
					$this->throwException ( 'Set cache of key ' . $key . ' failed', StatusCode::SET_MEMCACHE_ERROR );
				} else {
					$this->throwException ( 'Set to memcache failed', StatusCode::SET_MEMCACHE_ERROR );
				}
			}
		}
	}
	/**
	 * 将一个值从缓存里面删除
	 */
	protected function delToCache($key, $gameuid) {
		if ($this->useMemcache [$this->model]) {
			if (empty ( $gameuid )) {
				$gameuid = $this->gameuid;
			}
			$cache = $this->getCacheInstanceNoHash ( $gameuid );
			return $cache->delete ( $key );
		}
	}
	
	protected function getUserIp() {
		return $_SERVER ["REMOTE_ADDR"];
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
	
	protected function getDbInfo($table, $gameuid = null) {
		if ($gameuid == null) {
			$gameuid = $this->gameuid;
		}
		// 获取数据库配置
		$table_info = $this->sns_id ? $this->dbconfig [$table] [$sns_id] : $this->dbconfig [$table];
		if (empty ( $table_info )) {
			$table_info = $this->dbconfig ['default'];
		}
		
		if (empty ( $table_info )) {
			$this->throwException ( 'db map not set: key ' . $table, 1 );
		}
		
		$host_num = intval ( $table_info ['host_num'] );
		if ($host_num < 1) {
			$host_num = 1;
		}
		
		$db_name = strval ( $table_info ['db_name'] );
		$db_num = intval ( $table_info ['db_num'] );
		if ($db_num < 1) {
			$db_num = 1;
		}
		$table_num = intval ( $table_info ['table_num'] );
		if ($table_num < 1) {
			$table_num = 1;
		}
		
		$table_name = $table;
		if ($table_num > 1) {
			$table_name = $table_name . '_' . intval ( $gameuid % $table_num );
		}
		
		// 逻辑分服
		if (in_array ( $table, $this->dispatchTable )) {
			$table_name = $table_name . '__' . intval ( $this->server );
		}
		// echo $table_name;
		$dbhost = null;
		// 分库
		if ($db_num > 1) {
			$db_index = intval ( $gameuid / $table_num % $db_num );
			$db_name = $db_name . '_' . $db_index;
			// db host
			if ($host_num > 1) {
				$hosts = explode ( ",", $table_info ['host'] ); // host1:0-4,host2:5-9
				foreach ( $hosts as $host_string ) {
					$pos = strrpos ( ':' . $host_string, ':' );
					$area = substr ( $host_string, $pos );
					$host_name = substr ( $host_string, 0, $pos - 1 );
					$areas = explode ( "-", $area );
					$min = intval ( $areas [0] );
					$max = intval ( $areas [1] );
					if ($db_index >= $min && $db_index <= $max) {
						$dbhost = $this->dbconfig [$host_name];
						break;
					}
				}
			} else {
				$host = $table_info ['host'];
				$dbhost = $this->dbconfig [$host];
			}
		} else {
			// 不分库
			$host = $table_info ['host'];
			$dbhost = $this->dbconfig [$host];
		}
		if ($dbhost == null) {
			$this->throwException ( "table config of $table is not correct!", 2 );
		}
		$this->host = $dbhost ['host'];
		$this->user = $dbhost ['user'];
		$this->password = $dbhost ["password"];
		$this->db_name = $db_name;
		$this->table_name = $table_name;
		return true;
	}
	
	/**
	 * 是否在配置文件配置了该项配置
	 * @param $config_key string
	 *       	 配置项名称
	 * @return bool
	 */
	protected function issetConfig($config_key) {
		return array_key_exists ( $config_key, $GLOBALS ['config'] );
	}
	
	/**
	 * 获取配置的config
	 * 
	 * @param $config_key string       	
	 * @return mixed
	 */
	protected function getConfig($config_key) {
		if (empty ( $config_key ) || is_array ( $config_key )) {
			return null;
		}
		if (isset ( $GLOBALS ['config'] [$config_key] )) {
			return $GLOBALS ['config'] [$config_key];
		}
		return null;
	}
	
	protected function getValues($data) {
		foreach ( $data as $v ) {
			$str .= "'" . $v . "',";
		}
		$str = rtrim ( $str, ',' );
		return $str;
	}
	
	protected function getKeyValueStr($data) {
		foreach ( $data as $key => $v ) {
			$str .= $key . "='" . $v . "',";
		}
		$str = rtrim ( $str, ',' );
		return $str;
	}
	
	// 数据库逻辑分服(只可用于普通sql语句执行部分)
	protected function getDispatchTableName($table) {
		if (in_array ( $table, $this->dispatchTable )) {
			return $table . '__' . $this->server;
		}
		return $table;
	}
	/**
	 * 使用handlersocket插入一条数据
	 *
	 * @param $tablename 表名       	
	 * @param $gameuid 用户游戏内uid       	
	 * @param $values 要插入的数据array，key为字段名       	
	 */
	public function hsInsert($tablename, $gameuid, $values) {
		$this->insertCount ++;
		$dbhelper = $this->getMysqlInstance ( $tablename, $tablename, $gameuid );
		$fields = implode ( ',', array_keys ( $values ) );
		$value = $this->getValues ( $values );
		$sql = 'insert into ' . $this->db_name . '.' . $this->table_name . ' (' . $fields . ') values(' . $value . ')';
		return $dbhelper->execute ( $sql );
	
	}
	
		/**
	 * 使用handlersocket批量插入多条数据
	 *
	 * @param $tablename unknown_type       	
	 * @param $gameuid unknown_type       	
	 * @param $values unknown_type       	
	 */
	public function hsMultiInsert($tablename, $gameuid, $values) {
		$this->insertCount ++;
		// 使用传统的mysql方式
		$dbhelper = $this->getMysqlInstance ( $tablename, $tablename, $gameuid );
		$fields = implode ( ',', array_keys ( $values [0] ) );
		$sql = 'insert into ' . $this->db_name . '.' . $this->table_name . ' (' . $fields . ') values';
		foreach ( $values as $v ) {
			$value = $this->getValues ( $v );
			$sql .= '(' . $value . '),';
		}
		$sql = rtrim ( $sql, ',' );
		$dbhelper->execute ( $sql );
		return true;
	}
	
		/**
	 * 使用handlersocket更新一条数据
	 *
	 * @param $tablename 表名       	
	 * @param $gameuid 用户游戏内uid       	
	 * @param $values 要更新的数据array，key为字段名       	
	 * @param
	 *       	 pk_value 指定的主键值
	 */
	public function hsUpdate($tablename, $gameuid, $values, $pk_value) {
		$this->updateCount ++;
		// 使用传统的mysql方式
		$dbhelper = $this->getMysqlInstance ( $tablename, $tablename, $gameuid );
		$values_str = $this->getKeyValueStr ( $values );
		if (empty ( $pk_value )) {
			$where_str = '1=1';
		} else {
			foreach ( $pk_value as $key => $v ) {
				$where_str .= $key . "='" . $v . "' and ";
			}
			$where_str = rtrim ( $where_str, " and " );
		}
		$sql = 'update ' . $this->db_name . '.' . $this->table_name . ' set ' . $values_str . ' where ' . $where_str;
		return $dbhelper->execute ( $sql );
	}
	
		/**
	 *
	 * @param $tablename 表名       	
	 * @param $gameuid 用户游戏内uid       	
	 * @param
	 *       	 要获取的数据字段列名，$fields 字符串形式 ，逗号分隔
	 * @param
	 *       	 indexname where条件key=value中的key 该key必须是索引键
	 * @param
	 *       	 values where条件key=value 中的value array形式，可以是多个值
	 */
	public function hsSelectOne($tablename, $gameuid, $fields, $values, $indexname = '') {
		$this->selectCount ++;
		// 使用传统的mysql方式
		$dbhelper = $this->getMysqlInstance ( $tablename, $tablename, $gameuid );
		if (empty ( $values )) {
			$where_str = '1=1';
		} else {
			foreach ( $values as $key => $v ) {
				$where_str .= $key . "='" . $v . "' and ";
			}
			$where_str = rtrim ( $where_str, " and " );
		}
		$sql = 'select ' . $fields . ' from ' . $this->db_name . '.' . $this->table_name . ' where ' . $where_str;
		return $dbhelper->getOne ( $sql );
	}
	
		/**
	 * 使用handlersocket获取所有的记录
	 *
	 * @param $tablename 表名       	
	 * @param $gameuid 用户游戏内uid       	
	 * @param
	 *       	 要获取的数据字段列名，$fields 字符串形式 ，逗号分隔
	 * @param
	 *       	 indexname where条件key=value中的key 该key必须是索引键
	 * @param
	 *       	 values where条件key=value 中的value array形式，可以是多个值
	 */
	public function hsSelectAll($tablename, $gameuid, $fields, $values, $indexname = '', $limit = -1, $offset = 0, $op = '=') {
		$this->selectCount ++;
		// 使用传统的mysql方式
		$dbhelper = $this->getMysqlInstance ( $tablename, $tablename, $gameuid );
		if (empty ( $values )) {
			$where_str = '1=1';
		} else {
			foreach ( $values as $key => $v ) {
				$where_str .= $key . $op . "'" . $v . "' and ";
			}
			$where_str = rtrim ( $where_str, " and " );
		}
		$sql = 'select ' . $fields . ' from ' . $this->db_name . '.' . $this->table_name . ' where ' . $where_str;
		if ($limit > 0 && $offset > 0) {
			$sql .= ' limit ' . $offset . ',' . $limit;
		} else if ($limit > 0) {
			$sql .= ' limit ' . $limit;
		}
		return $dbhelper->getAll ( $sql );
	}
	
	/**
	 * 单主键时in条件可用
	 *
	 * @param $tablename 表名       	
	 * @param $gameuid 用户游戏内uid       	
	 * @param
	 *       	 要获取的数据字段列名，$fields 字符串形式 ，逗号分隔
	 * @param $values where条件中的value值
	 *       	 例如 $values=array(1,2,3)
	 * @param
	 *       	 indexname where条件key=value中的key 该key必须是索引键
	 * @param
	 *       	 where 条件中的key 名字 字符串格式
	 * @return unknown
	 */
	public function hsSelectIn($tablename, $gameuid, $fields, $values, $indexname = '', $where_keyname = '') {
		if (empty ( $where_keyname )) {
			$this->throwException ( 'by mysql need key name!', 222 );
		}
		$dbhelper = $this->getMysqlInstance ( $tablename, $tablename, $gameuid );
		if (empty ( $values )) {
			$where_str = '1=1';
		} else {
			foreach ( $values as $v ) {
				$where_str .= "'" . $v . "',";
			}
			
			$where_str = $where_keyname . ' in (' . rtrim ( $where_str, ',' ) . ')';
		}
		$sql = 'select ' . $fields . ' from ' . $this->db_name . '.' . $this->table_name . ' where ' . $where_str;
		return $dbhelper->getAll ( $sql );
	}
	
		/**
	 * 使用handlersocket获取多条记录或多主键时
	 *
	 * @param $tablename 表名       	
	 * @param $gameuid 用户游戏内uid       	
	 * @param
	 *       	 要获取的数据字段列名，$fields 字符串形式 ，逗号分隔
	 * @param
	 *       	 indexname where条件key=value中的key 该key必须是索引键
	 * @param
	 *       	 values where条件key=value 中的value array形式，可以是多个值
	 */
	public function hsSelectMulti($tablename, $gameuid, $fields, $valuesarr, $indexname = '') {
		$this->selectCount ++;
		
		// 使用传统的mysql方式
		$dbhelper = $this->getMysqlInstance ( $tablename, $tablename, $gameuid );
		if (empty ( $valuesarr )) {
			$this->throwException ( 'by mysql need key name!', 564 );
		}
		$data = array ();
		foreach ( $valuesarr as $values ) {
			$where_str = '';
			foreach ( $values as $key => $v ) {
				$where_str .= $key . "='" . $v . "' and ";
			}
			$where_str = rtrim ( $where_str, " and " );
			$sql = 'select ' . $fields . ' from ' . $this->db_name . '.' . $this->table_name . ' where ' . $where_str;
			$res = $dbhelper->getOne ( $sql );
			if (! empty ( $res ))
				$data [] = $res;
		}
		return $data;
	
	}
	
	/**
	 * @param $tablename 表名       	
	 * @param $gameuid 用户游戏内uid       	
	 * @param
	 *       	 要删除的数据where = key为字段名的 $values 必须有key
	 */
	public function hsDelete($tablename, $gameuid, $values, $indexname = '') {
		$this->delCount ++;
		$dbhelper = $this->getMysqlInstance ( $tablename, $tablename, $gameuid );
		$where_str = '';
		foreach ( $values as $key => $v ) {
			$where_str .= $key . "='" . $v . "' and ";
		}
		$where_str = rtrim ( $where_str, " and " );
		$sql = 'delete from ' . $this->db_name . '.' . $this->table_name . ' where ' . $where_str;
		return $dbhelper->execute ( $sql );
	}
	
	protected function getHsConfig() {
		return array ('host' => $this->host, 'dbname' => $this->db_name, 'table' => $this->table_name );
	}
	
	/**
	 * 抛出程序的异常
	 * 
	 * @param $message string
	 *       	 错误消息
	 * @param $code int
	 *       	 错误代码
	 * @param $uid int
	 *       	 相关的用户uid
	 * @param $gameuid int
	 *       	 相关的用户gameuid
	 * @param $exit bool
	 *       	 是否输出错误消息后终止脚本
	 * @return void
	 */
	protected function throwException($message, $code, $uid = null, $gameuid = null, $exit = true) {
		$modelName = $this->model;
		GameException::throwException ( $message, $code, $uid ? $uid : $this->uid, $gameuid ? $gameuid : $this->gameuid, $exit, $modelName );
	}
	
	/*
	 * redis相关的操作
	 */
	protected function addToSortedSet($key, $member, $sorce) {
		if ($this->useRedis) {
			if (empty ( $this->rediska )) {
				$this->createRedis ();
			}
			return $this->rediska->addToSortedSet ( $key, $member, $sorce );
		
		}
		return false;
	}
	
	protected function getRankFromSortedSet($key, $member) {
		if ($this->useRedis) {
			if (empty ( $this->rediska )) {
				$this->createRedis ();
			}
			return $this->rediska->getRankFromSortedSet ( $key, $member );
		}
		return false;
	}
	
	protected function getFromSortedSetByScore($key, $min, $max, $limit, $offect) {
		if ($this->useRedis) {
			if (empty ( $this->rediska )) {
				$this->createRedis ();
			}
			return $this->rediska->getFromSortedSetByScore ( $key, $min, $max, false, $limit, $offect );
		}
		return false;
	}
	
	public function commandAnalysis() {
		return array ('model' => get_class ( $this ), 'insert' => $this->insertCount, 'update' => $this->updateCount, 'select' => $this->selectCount, 'del' => $this->delCount );
	}
}
