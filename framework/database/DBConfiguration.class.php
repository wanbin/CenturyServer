<?php
/**
 * 数据库的连接配置类
 * @author shusl
 *
 */
class DBConfiguration {
	const DB_DRIVER_MYSQL = 'mysql';
	const DB_DRIVER_MSSQL_NT = 'mssqlnt';
	
	public $driver = self::DB_DRIVER_MYSQL;
	public $host = 'localhost';
	public $port = 3306;
	public $database = '';
	public $username = '';
	public $password = '';
	public $charset = 'utf8';
	public $newlink = false;
	public $pconnect = false;
	public $client_flag = 0;
	protected $dsn = '';

	public function __construct($config){
		$this->init($config);
	}
	/**
	 * 初始化函数
	 * @param $config 可以使用数组或者dsn来配置数据库
	 * 数组可以使用如下key来配置
	 * driver     : 访问数据库使用的驱动，可以是mysql和mssqlnt。默认是mysql
	 * host       : 数据库主机的ip地址
	 * port       : 数据库的访问端口，默认是3306,如果不是该值，那么这个值会附加到host参数后面。
	 * dbname     : 数据库的名称
	 * database   : 数据库的名称，和dbname参数相同
	 * username   : 访问数据库的用户名
	 * passwork   : 访问数据库的密码
	 * charset    : 客户端使用的编码，默认是utf8
	 * newlink    : 是否需要创建新连接来连接数据库，默认为false
	 * pconnect   : 是否使用持久连接，默认为false
	 * client_flag: 连接数据库使用的flag，连接mysql的时候，可能会用到，一般需要设置该选项
	 * 也可以使用dsn字符串来设置，格式如下：
	 * driver://username:password@host/database?option=opt_value
	 * @return void
	 */
	public function init($config){
		if(is_array($config)){
			$this->setConfig($config);
		}elseif(!empty($config)){
			$db_config = $this->parseDsn($config);
			if($db_config !== false){
				$this->setConfig($db_config);
				$this->dsn = $config;
			}
		}
	}
	
	protected function setConfig(array $config){
		if(isset($config['driver'][0])){
			$this->driver = strtolower(trim($config['driver']));
		}
		if(isset($config['host'][0])){
			$this->host = trim($config['host']);
		}
		if(isset($config['port'])){
			$this->port = intval($config['port']);
			// 如果设置了不同于3306的端口，则将它加入到host后面
			if($this->port != 3306 && strpos($this->host,':') === false){
				$this->host .= ':' . $this->port;
			}
		}
		if(isset($config['dbname'])){
			$this->database = trim($config['dbname']);
		}
		if(isset($config['database'])){
			$this->database = trim($config['database']);
		}
		if(isset($config['username'])){
			$this->username = trim($config['username']);
		}
		if(isset($config['password'][0])){
			$this->password = trim($config['password']);
		}
		if(isset($config['charset'][0])){
			$this->charset = trim($config['charset']);
		}
		if(isset($config['newlink'])){
			$this->newlink = (bool) $config['newlink'];
		}
		if(isset($config['pconnect'])){
			$this->pconnect = (bool) $config['pconnect'];
		}
		if(isset($config['client_flag'])){
			$this->client_flag = $config['client_flag'];
		}
	}
	/**
	 * 根据dsn解析出数据库的配置。
	 * dsn的格式是：driver://username:password@host/database?option=opt_value
	 */
	protected function parseDsn($db_dsn){
		if(!isset($db_dsn[0])){
			return false;
		}
		$dsn = parse_url($db_dsn);
		if(empty($dsn)){
			return false;
		}
		$config['driver'] = strtolower($dsn['scheme']);
		$config['host'] = $dsn['host'];
		if(!empty($dsn['port'])){
			$config['host'] .= ':' . intval($dsn['port']);
		}
		$config['password'] = $dsn['pass'];
		$config['username'] = $dsn['user'];
		if(isset($dsn['path'][1])){
			$config['database'] = substr($dsn['path'],1);
		}
		$config['charset'] = 'utf8';
		$config['newlink'] = false;
		$config['pconnect']	= false;
		if(isset($dsn['query'])){
			parse_str($dsn['query'],$opt);
			$config = array_merge($config,$opt);
		}
		return $config;
	}
}

