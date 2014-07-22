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
	protected $channel="ANDROID";
	
	//数据分析参数
	protected $selectCount = 0;
	protected $insertCount = 0;
	protected $updateCount = 0;
	protected $deleteCount = 0;
	
	protected $itemMC = null;
	protected $mysqlConnect=null;
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
				if(strlen($uid)==strlen("5A74E27E8AC44C778731B7A8A8207250")){
					$this->channel='IOS';
				}elseif(substr($uid, 1,5)==substr("ouHjQjpu175ug-jVh0Wdw5i--Xgw", 1,5)){
					$this->channel='WX';
				}
				$sql = "insert into wx_account(uid,regtime,channel) values('$uid',$time,'".$this->channel."')";
				$this->oneSql ( $sql );
				$res = $this->oneSqlSignle ( "select * from wx_account where uid='$uid'" );
			}
		}
		return $res['gameuid'];
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
		if (ISBAIDU) {
			$sqlarr = explode ( ';', $sql );
			$ret = null;
			foreach ( $sqlarr as $key => $value ) {
				$ret = $this->BaiduContent ( $sql );
			}
			return $ret;
		}
		
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
				$DBHandler->execute ( $value );
			}
			return;
		} else {
			return $DBHandler->getAll ( $sql );
		}
	}
	
	protected function BaiduContent($sql) {
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
		return $ret;
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
		$fileName = date ( "Y-m-d", time () ) . "sqlerror.sql";
		$temtime = date ( "Y-m-d H:i:s", time () );
		$strAdd = "#[$temtime]\n";
		file_put_contents ( PATH_ROOT . "/log/$fileName", $strAdd . $e . $sql, FILE_APPEND );
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
