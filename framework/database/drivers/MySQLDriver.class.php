<?php
  
class MySQLDriver
{
	const ERROR_CODE_LOST_CONNECTION = 2006;
	protected static $queryCount = 0;
	//note:查询时间
	protected static $queryTimes = 0;
	private $conn;
	private $result;
	private $rsType = MYSQL_ASSOC;
	private $db_config;
	
	private $throwException = true;
	/**
	 * 如果运行在silent mode，则不会产生错误
	 * @var bool
	 */
	private $silent_mode = false;
	
	/**
	 * 设置是否在发生错误的时候抛出异常，如果设置为false，
	 * 并且silent mode设置为false，则产生一个E_USER_ERROR级别的错误
	 * @param $value
	 * @return void
	 */
	public function throwExceptionOnError($value = true){
		$this->throwException = $value ? true : false;
	}
	
	/**
	 * 获取执行SQL查询所用的时间
	 * @return float
	 */
	public static function getQueryTimes() {
		return self::$queryTimes;
	}
	
	protected function addQueryTime($start){
		$duration = microtime(true) - $start;
		self::$queryTimes += $duration;
		return $duration;
	}
	
	/**
	 * 获得执行查询的次数
	 * @return int
	 */
	public static function getQueryCount() {
		return self::$queryCount;
	}
	
	
	/**
	 * 连接数据库
	 *
	 * @param string $dbHost
	 * @param string $dbName
	 * @param string $dbUser
	 * @param string $dbPass
	 * @param blooean $dbOpenType
	 * @param string $dbCharset
	 * @return void
	 */
	public function connect($dbHost = '', $dbUser = '', $dbPass = '', $dbName = '', $dbOpenType = false ,$dbCharset = 'utf8',$newlink = false) {
		$this->db_config = array('host' => $dbHost,	'user' => $dbUser,
				'pass' => $dbPass,'dbname' => $dbName,
				'newlink' => $newlink,'charset' => $dbCharset,
				'pconnect' => $dbOpenType);
		$this->conn = $this->_connect($this->db_config,$dbOpenType);
		$this->setCharset($dbCharset);
		$this->selectDB($dbName);
		return true;
	}
	/**
	 * 尝试重新连接到数据库
	 */
	protected function reconnect(){
		$this->conn = $this->_connect($this->db_config,$this->db_config['pconnect'],true);
		if($this->conn){
			$this->setCharset($this->db_config['charset']);
			$this->selectDB($this->db_config['dbname']);
			return true;
		}
		return false;
	}
	/**
	 * 设置字符集
	 * @param string $charset
	 * @return boolean|resource
	 */
	protected function setCharset($charset = 'utf8'){
		if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
			// the preferred way to set charset
			return mysql_set_charset($charset,$this->conn);
		}else{
			return mysql_query('SET NAMES ' . $charset,$this->conn);
		}
	}
	
	protected function _connect($config,$pconnect = false,$silent = false){
		if($pconnect){
			$re = mysql_pconnect($config['host'], $config['user'], $config['pass']);
			if(!$re && !$silent){
				$this->errorMsg('Can not pconnect to MySQL server ' . $config['host']);
			}
		}else{
			$re = mysql_connect($config['host'], $config['user'], $config['pass'],$config['newlink']);
			if(!$re && !$silent){
				$this->errorMsg('Can not connect to MySQL server ' . $config['host']);
			}
		}
		return $re;
	}
	/**
	 * 选择一个数据库
	 * @param $dbName
	 * @return bool
	 */
	public function selectDB($dbName){
		if(isset($dbName[0])){
			$re = mysql_select_db($dbName, $this->conn);
			if(!$re){
				$this->errorMsg('select db failure',1);
			}
		}
		return true;
	}
	/**
	 * Ping a server connection or reconnect if there is no connection
	 */
	public function ping(){
		return mysql_ping($this->conn);
	}
	/**
	 * 关闭数据库连接，当您使用持久连接时不会真正关闭连接
	 *
	 * @return boolean
	 */
	public function close() {
		return mysql_close($this->conn);
	}
	
	/**
	 * 发送查询语句
	 *
	 * @param string $sql
	 * @return blooean
	 */
	public function query($sql) {
		if(!isset($sql[0])){
			return false;
		}
		$start = microtime(true);
		$this->result = mysql_query($sql, $this->conn);
		$this->addQueryTime($start);
		++self::$queryCount;
		if(!$this->result) {
			// if the lost connection to server, try to connect and
			// retry to execute the query
			if(self::ERROR_CODE_LOST_CONNECTION == $this->getErrorCode()){
				// try to reconnect
				if($this->reconnect()){
					$this->result = mysql_query($sql, $this->conn);
				}
			}
			if(!$this->result) {
				$this->errorMsg('MySQL Query Error', $sql);
			}
		}
		return $this->result;
	}
	/**
	 * 设置取数据的类型
	 * @param $type ASSOC, NUM, BOTH
	 */
	public function setFetchType($type = "ASSOC"){
		$this->rsType = $type != "ASSOC" ? ($type == "NUM" ? MYSQL_NUM : MYSQL_BOTH) : MYSQL_ASSOC;
	}
	/**
	 * 设置是否启用silent mode，如果启用了该mode，并且没有设置
	 * 抛出异常，则执行sql出错时候，不会产生错误，只是返回false
	 * @param $silent
	 * @return void
	 */
	public function setSilentMode($silent = true){
		$this->silent_mode = (bool)$silent;
	}
	
	public function quote($str){
		return mysql_real_escape_string($str,$this->conn);
	}
	
	/**
	 * 数据量比较大的情况下查询
	 *
	 * @param string $sql
	 * @param string $type
	 * @return blooean
	 */
	public function bigQuery($sql, $type = "ASSOC") {
		if(!isset($sql[0])){
			return false;
		}
		$this->rsType = $type != "ASSOC" ? ($type == "NUM" ? MYSQL_NUM : MYSQL_BOTH) : MYSQL_ASSOC;
		$start = microtime(true);
		$this->result = mysql_unbuffered_query($sql, $this->conn);
		$this->addQueryTime($start);
		++self::$queryCount;
		if(!$this->result) {
			if(self::ERROR_CODE_LOST_CONNECTION == $this->getErrorCode()){
				// try to reconnect
				if($this->reconnect()){
					$this->result = mysql_unbuffered_query($sql, $this->conn);
				}
			}
			if(!$this->result) {
				return $this->errorMsg('MySQL Query Error', $sql);
			}
		}
		return $this->result;
	}
	
	/**
	 * 获取全部数据
	 *
	 * @param string $sql
	 * @param blooean $nocache
	 * @return array
	 */
	public function getAll($sql, $noCache = false) {
		$noCache ? $this->bigQuery($sql) : $this->query($sql);
		$rows = array();
		while($row = mysql_fetch_array($this->result, $this->rsType)) {
			$rows[] = $row;
		}
		return $rows;
	}
	/**
	 * 执行SQL，以对象的数组的形式返回，如果查询结果为空，则返回空数组
	 * @param $sql
	 * @param $class_name 保存对象的类名
	 * @param $params 用于创建对象的参数
	 * @param $noCache
	 * @return array
	 */
	public function getObjectAll($sql,$class_name = 'stdClass', $params = null,$noCache = false){
		$noCache ? $this->bigQuery($sql) : $this->query($sql);
		$rows = array();
		while($row = mysql_fetch_object($this->result, $class_name,$params)) {
			$rows[] = $row;
		}
		return $rows;
	}
	/**
	 * 根据指定的字段，从查询结果中取得一个pair关联数组
	 * @param string $sql
	 * @param string $key_field 用作数组key的字段
	 * @param string $value_field 用作value的字段
	 * @param bool $noCache 是否使用缓存查询
	 * @return array
	 */
	public function getPairs($sql,$key_field,$value_field,$noCache = false){
		$noCache ? $this->bigQuery($sql) : $this->query($sql);
		$rows = array();
		while($row = mysql_fetch_array($this->result, $this->rsType)) {
			$rows[$row[$key_field]] = $row[$value_field];
		}
		return $rows;
	}
	/**
	 * 根据取得指定字段的数组，相当于取得结果中的某一列的值
	 * @param $sql
	 * @param $value_field
	 * @param $noCache
	 * @return array
	 */
	public function getColumn($sql,$column,$noCache = false){
		$noCache ? $this->bigQuery($sql) : $this->query($sql);
		$rows = array();
		while($row = mysql_fetch_array($this->result, $this->rsType)) {
			$rows[] = $row[$column];
		}
		return $rows;
	}
	
	/**
	 * 获取全部数据,结果是以数据行中指定的字段为key的关联数组
	 *
	 * @param string $sql
	 * @param string $key 作为key的字段
	 * @param blooean $nocache
	 * @return array
	 */
	public function getAssocAll($sql,$key,$noCache = false) {
		$noCache ? $this->bigQuery($sql) : $this->query($sql);
		$rows = array();
		while($row = mysql_fetch_array($this->result, $this->rsType)) {
			$rows[$row[$key]] = $row;
		}
		return $rows;
	}
	/**
	 * 获取全部数据,组成以数据行中指定的字段和分界字符的字符串
	 *
	 * @param string $sql
	 * @param string $key 作为key的字段
	 * @param string $delimiter
	 * @param blooean $nocache
	 * @return string
	 */
	public function getAndJoinAll($sql,$key,$delimiter = ',', $noCache = false) {
		$noCache ? $this->bigQuery($sql) : $this->query($sql);
		$rows = array();
		$rs = '';
		while($row = mysql_fetch_array($this->result, $this->rsType)) {
			$rs .= $row[$key] . $delimiter;
		}
		return rtrim($rs,$delimiter);
	}
	/**
	 * 获取单行数据
	 *
	 * @param string $sql
	 * @return array
	 */
	public function getOne($sql) {
		$this->query($sql);
		$rows = mysql_fetch_array($this->result, $this->rsType);
		return $rows;
	}
	
	/**
	 * 从结果集中取得一行作为关联数组，或数字数组
	 *
	 * @param resource $query
	 * @return array
	 */
	public function fetchArray($query) {
		return mysql_fetch_array($query, $this->rsType);
	}
	
	/**
	 * 取得结果数据
	 *
	 * @param resource $query
	 * @return string
	 */
	public function result($query, $row,$field = 0) {
		$query = mysql_result($query, $row,$field);
		return $query;
	}
	
	/**
	 * 取得上一步 INSERT 操作产生的 ID
	 *
	 * @return integer
	 */
	
	public function insertId() {
		return ($id = mysql_insert_id($this->conn)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}
	
	/**
	 * 取得行的数目
	 *
	 * @param resource $query
	 * @return integer
	 */
	public function numRows($query) {
		return mysql_num_rows($query);
	}
	
	/**
	 * 取得结果集中字段的数目
	 *
	 * @param resource $query
	 * @return integer
	 */
	public function numFields($query) {
		return mysql_num_fields($query);
	}
	
	/**
	 * 取得前一次 MySQL 操作所影响的记录行数
	 *
	 * @return integer
	 */
	public function affectedRows() {
		return mysql_affected_rows($this->conn);
	}
	
	/**
	 * 取得结果中指定字段的字段名
	 *
	 * @param string $data
	 * @param string $table
	 * @return array
	 */
	public function listFields($data, $table) {
		$row = mysql_list_fields($data, $table, $this->conn);
		$count = mysql_num_fields($row);
		for($i = 0; $i < $count; $i++) {
			$rows[] = mysql_field_name($row, $i);
		}
		return $rows;
	}
	
	/**
	 * 列出数据库中的表
	 *
	 * @param string $data
	 * @return array
	 */
	public function listTables($data) {
		$query = mysql_list_tables($data);
		$rows = array();
		while($row = mysql_fetch_array($query)) {
			$rows[] = $row[0];
		}
		mysql_free_result($query);
		return $rows;
	}
	
	/**
	 * 取得表名
	 *
	 * @param string $table_list
	 * @param integer $i
	 * @return string
	 */
	public function tableName($table_list, $i) {
		return mysql_tablename($table_list, $i);
	}
	
	/**
	 * 转义字符串用于查询
	 *
	 * @param string $char
	 * @return string
	 */
	public function escapeString($char) {
		return mysql_escape_string($char);
	}
	
	/**
	 * 取得数据库版本信息
	 *
	 * @return string
	 */
	public function getVersion() {
		return mysql_get_server_info();
	}
	/**
	 * 获取mysql的错误代码
	 */
	public function getErrorCode(){
		return mysql_errno($this->conn);
	}
	/**
	 * 获取mysql的错误消息
	 * @return unknown_type
	 */
	public function getErrorMessage(){
		return mysql_error($this->conn);
	}
	
	/**
	 * 错误处理
	 *
	 * @param string $msg
	 * @param string $sql
	 * @return void
	 */
	protected function errorMsg($msg = '', $sql = '') {
		$message = '';
		if(isset($msg[0])) {
			$message .=  "ErrorMsg:$msg; ";
		}
		if(isset($sql[0])) {
			$message .=  "SQL: $sql; ";
		}
		$message .=  "Error: " . $this->getErrorMessage()." ";
		$message .=  "Errno: " . $this->getErrorCode();
		if($this->throwException){
		}elseif($this->silent_mode){
			return false;
		}
		trigger_error($message,E_USER_ERROR);
		return false;
	}
}

?>