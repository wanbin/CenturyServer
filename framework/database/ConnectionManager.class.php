<?php

require_once FRAMEWORK .'/database/drivers/MooMySQL.class.php';

class ConnectionManager{
	private static $read_conn_pool = array();
	private static $write_conn = null;
	private static $current_index = 0;
	private $total_read_count = 1;
	private $config = null;
	private $read_hosts;
	private $write_host;

	public function __construct($config){
		$this->setConfig($config);
	}

	public function __destruct(){
		$this->closeAll();
	}

	public function setConfig($config){
		$this->config = $config;
		$host = $config['host'];
		if(strpos($host,',') === false){
			$this->write_host = $host;
			$this->read_hosts = $host;
			$this->total_read_count = 0;
		}
		else{
			$hosts = explode(',',$config['host']);
			$this->write_host = array_shift($hosts);
			if(count($hosts) >= 1 ){
				$this->read_hosts = $hosts;
			}
			$this->total_read_count = count($this->read_hosts);
		}
	}

	private function createConnection($host){
		$dbconfig = $this->config;
		$db = new MooMySQL();
		$db->connect($host,$dbconfig['username'],
		$dbconfig['password'],$dbconfig['dbname'],
			$dbconfig['pconnect'],$dbconfig['encoding'],$dbconfig['newlink']);
		return $db;
	}

	public function closeAll(){
		$this->closeWriteConnection();
		if($this->total_read_count >= 1 && is_array(self::$read_conn_pool)){
			foreach (self::$read_conn_pool as $key => $db) {
				if(!is_null($db)){
					$db->close();
					self::$read_conn_pool[$key] = null;
				}
			}
			self::$read_conn_pool = null;
		}
	}

	public function closeWriteConnection(){
		if(self::$write_conn != null){
			self::$write_conn->close();
			self::$write_conn = null;
		}
	}

	/**
	 * 返回一个数据库对象,该服务器为写数据准备
	 *
	 * @return MooMySQL
	 */
	public function getWriteConnection(){
		if(self::$write_conn != null){
			return self::$write_conn;
		}
		self::$write_conn = $this->createConnection($this->write_host);
		return self::$write_conn;
	}

	public function closeReadConnection(){
		if($this->total_read_count < 1){
			$this->closeWriteConnection();
			return;
		}
		if(!is_null(self::$read_conn_pool[self::$current_index])){
			self::$read_conn_pool[self::$current_index]->close();
			self::$read_conn_pool[self::$current_index] = null;
		}
	}

	/**
	 * 返回一个数据库对象,该服务器为读数据准备
	 *
	 * @return MooMySQL
	 */
	public function getReadConnection(){
		// 只有一个服务器，则读写为同一个服务器
		if($this->total_read_count < 1){
			return $this->getWriteConnection();
		}
		// 如果当前的连接没有关闭，则使用当前的连接
		if(!is_null(self::$read_conn_pool[self::$current_index])){
			return self::$read_conn_pool[self::$current_index];
		}
		// 取下一个读的服务器
		++self::$current_index;
		if(self::$current_index > $this->total_read_count - 1){
			self::$current_index = 0;
		}
		// 如果所需的连接没有打开，则打开该连接
		if(!isset(self::$read_conn_pool[self::$current_index]) ||
		is_null(self::$read_conn_pool[self::$current_index])){
			self::$read_conn_pool[self::$current_index] = $this->createConnection(
			$this->read_hosts[self::$current_index]);
		}
		return self::$read_conn_pool[self::$current_index];
	}
}
?>