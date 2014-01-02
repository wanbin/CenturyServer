<?php
require_once FRAMEWORK . 'exception/ExceptionConstants.php';
require_once 'MemcacheConstants.php';

class BaseModel {
	
	public $gameuid = NULL;
	protected $uid     = NULL;
	protected $server  = NULL;
	protected $useMemcache = null;
	protected $model = null;
	protected $rediska = null;
	protected $useRedis = null;
	protected $commandAnalysis = null;
	
	//数据分析参数
	protected $selectCount = 0;
	protected $insertCount = 0;
	protected $updateCount = 0;
	protected $deleteCount = 0;
	
	protected $itemMC = null;
	public function __construct($uid) {
		// 加载config
		$config = $GLOBALS ['config'];
		$this->useMemcache = $config ['memcache'];
		$this->model = get_class ( $this );
		$this->useRedis = $config ['redis'];
		if (isset ( $uid )) {
			$this->uid = $uid;
			$this->gameuid = $this->getGameuid($uid);
		}
	}

	public function getGameuid($uid){
		if (isset ( $uid )) {
			$res = $this->oneSqlSignle ( "select * from wx_account where uid='$uid'" );
			if (empty ( $res )) {
				$time = time ();
				$sql = "insert into wx_account(uid,regtime) values('$uid',$time)";
				$this->oneSql ( $sql );
				$res = $this->oneSqlSignle ( "select * from wx_account where uid='$uid'" );
			}
		}
		return $res['gameuid'];
	}
	
	//=================================MYSQL============================================//
	
	// 不支持同一业务的数据库水平部署在不同服务器上
	protected function getDBInstance($tableName) {
		static $DBInstances = array ();
		if (isset ( $DBInstances [$tableName] )) {
			return $DBInstances [$tableName];
		}
		$DBHandler = new DBHandler($tableName,$this->gameuid,$this->server);
		$DBInstances[$tableName] = $DBHandler;
		return $DBHandler;
	}
	
	/**
	 +----------------------------------------------------------
	 * 将fields 和 values 拆分开成字符串
	 +----------------------------------------------------------
	 * @param array $values
	 * @return array()
	 +----------------------------------------------------------
	 */
	private function splitValues($values)
	{
		$key = $val = $sec = '';
		foreach ($values as $tKey => $tVal )
		{
			$key .= $sec . '`' . $tKey . '`';
			$val .= $sec . '\'' . $tVal . '\'';
			$sec = ', ';
		}
	
		return array(
				'fields' => $key,
				'values' => $val
		);
	}
	
	/**
	 +----------------------------------------------------------
	 * 插入一条记录
	 +----------------------------------------------------------
	 * @param string $tableName
	 * @param array  array('field'=>$value1,'field2'=>$value2)
	 * @return Boolean
	 +----------------------------------------------------------
	 */
	public function hsInsert($tableName, $values) {
		$this->insertCount ++;
		$DBHandler = $this->getDBInstance ( $tableName );
		$param = $this->splitValues($values);
		$sql = 'INSERT INTO ' . $tableName . ' (' . $param['fields'] . ') VALUES (' . $param['values'] . ')';
		return $DBHandler->execute ( $sql );
	
	}
	
	public function oneSqlSignle($sql) {
		$ret=$this->oneSql($sql);
		return $ret[0];
	}
	
	public function oneSql($sql) {
		$DBHandler = $this->getDBInstance ( $this->getTableName () );
		$tem = explode ( ' ', $sql );
		if (in_array ( $tem [0], array (
				'insert',
				'update'
		) )) {
			return $DBHandler->execute ( $sql );
		} else {
			return $DBHandler->getAll ( $sql );
		}
	}
	protected function getTableName() {
		return '';
	}
	/**
	 +----------------------------------------------------------
	 * 插入多条记录
	 +----------------------------------------------------------
	 * @param string $tableName
	 * @param array  array(array('field'=>$val_1),array('field'=>$val_2));
	 * @return boolean
	 +----------------------------------------------------------
	 */
	public function hsMultiInsert($tableName, $values) {
		$this->insertCount ++;
		// 使用传统的mysql方式
		$DBHandler = $this->getDBInstance ( $tableName );
	
		$sec = $vStr = '';
		$params = array();
		foreach ( $values as $val ) {
			$params = $this->splitValues($val);
			$vStr  .= $sec . '(' . $params['values'] . ')';
			$sec = ',';
		}
	
		$sql = 'INSERT INTO ' . $tableName . ' (' . $params['fields'] . ') VALUES ' . $vStr;
	
		$DBHandler->execute ( $sql );
		return true;
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
	
	/**
	 +----------------------------------------------------------
	 * 连接where的字符串
	 +----------------------------------------------------------
	 * @param array $where
	 * @return string
	 +----------------------------------------------------------
	 */
	private function joinWhereStr($where)
	{
		if (empty ( $where )) {
			$str = '1=1';
		} else {
			foreach ( $where as $key => $val )
			{
				if (is_array($val))
				{
					$str .= $key . $val['op'] ."'" . $val['v'] . "' AND ";
				}else{
						
					$str .= $key . "='" . $val . "' AND ";
				}
			}
			$str = rtrim ( $str, " AND " );
		}
	
		return $str;
	}
	
	/**
	 +----------------------------------------------------------
	 * 更新一条记录
	 +----------------------------------------------------------
	 * @param string $tableName
	 * @param array $values    array('field'=>'val')
	 * @param array $where     array('field'=>'value','field2'=>array('op'=>'>','v'=>'30'))
	 * @return Int  更新的id
	 +----------------------------------------------------------
	 */
	public function hsUpdate($tableName, $values, $where) {
		$this->updateCount ++;
		// 使用传统的mysql方式
		$DBHandler = $this->getDBInstance($tableName);
		$values_str = $this->joinValuesStr ( $values );
		$where_str  = $this->joinWhereStr($where);
		$sql = 'UPDATE ' . $tableName . ' SET ' . $values_str . ' WHERE ' . $where_str;
		return $DBHandler->execute ( $sql );
	}
	
	
	/**
	 +----------------------------------------------------------
	 * 选择一条记录
	 +----------------------------------------------------------
	 * @param string $tableName
	 * @param array $fields
	 * @param array $where
	 +----------------------------------------------------------
	 */
	public function hsSelectOne($tableName, $fields, $where) {
		$this->selectCount ++;
		// 使用传统的mysql方式
		$DBHandler = $this->getDBInstance ( $tableName );
		$where_str= $this->joinWhereStr($where);
	
		$sql = 'SELECT ' . $fields . ' FROM ' . $tableName . ' WHERE ' . $where_str;
		try {
			return $DBHandler->getOne ( $sql );
		} catch ( Exception $e ) {
			$this->writeSqlError($sql, $e);
		}
		return '';
	}
	
	public function writeSqlError($sql, $e) {
		$fileName = date ( "Y-m-d", time () ) . "sqlerror.sql";
		$temtime = date ( "Y-m-d H:i:s", time () );
		$strAdd = "#[$temtime]\n";
		file_put_contents ( PATH_ROOT . "/log/$fileName", $strAdd . $e . $sql, FILE_APPEND );
	}
	
	public function hsSelectAll($tableName, $fields, $where, $limit = -1, $offset = 0) {
		$this->selectCount ++;
		// 使用传统的mysql方式
		$DBHandler = $this->getDBInstance ( $tableName );
	
		$where_str = $this->joinWhereStr( $where );
	
		$sql = 'SELECT ' . $fields . ' FROM ' . $tableName . ' WHERE ' . $where_str;
	
		if ($limit > 0)
		{
			$limit_str = ' limit ' . $limit;
			if ($offset > 0)
			{
				$limit_str = ' limit ' . $offset . ',' . $limit;
			}
	
			$sql .= $limit_str;
		}
	
		return $DBHandler->getAll ( $sql );
	}
	
	
	/**
	 +----------------------------------------------------------
	 * 删除记录
	 +----------------------------------------------------------
	 * @param string $tableName
	 * @param array  $where
	 * @return 删除的id
	 +----------------------------------------------------------
	 */
	public function hsDelete($tableName,$where)
	{
		if (empty($where)){
			return FALSE;
		}
		$this->delCount ++;
		$DBHandler = $this->getDBInstance ( $tableName );
	
		$where_str = $this->joinWhereStr( $where );
	
		$sql = 'DELETE FROM ' . $tableName . ' WHERE ' . $where_str;
	
		return $DBHandler->execute ( $sql );
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
			return $ret;
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
		
	//===========================================Redis=======================================//
	
	// 初始化redis 代码
	public function createRedis($redisHost = 'redis') {
		if ($this->useRedis) {
			if (isset ( $GLOBALS ['cacheHost'] [$redisHost] )) {
				$hostArr = explode ( ':', $GLOBALS ['cacheHost'][$redisHost] ['host'] );
				$servers = array (array ('host' => $hostArr [0], 'port' => $hostArr [1] ) );
				unset ( $this->rediska );
				$this->rediska = new Rediska ();
				$this->rediska->setServers ( $servers );
			} else {
				$this->rediska = new Rediska ();
			}
		}
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
	
	
	//=====================================Common=========================//
	public function commandAnalysis() {
		return array ('model' => get_class ( $this ), 'insert' => $this->insertCount, 'update' => $this->updateCount, 'select' => $this->selectCount, 'del' => $this->delCount );
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
}
