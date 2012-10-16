<?php
require_once FRAMEWORK . '/database/DBConfiguration.class.php';
class DBHelper2 {
	private $dsn;
	/**
	 * 数据库配置类
	 * @var DBConfiguration
	 */
	private $config;
	protected $options = null;
	protected $dbname = '';
	/**
	 * 数据库连接
	 * @var MooMySQL
	 */
	private $conn = null;
	private $autoclose = false;
	private $errhandler = null;
	private $table_prefix = '';
	
	public function __construct($config = null){
		if(!empty($config)){
			$this->setConfig($config);
		}
	}
	
	/**
	 * set the error handler
	 * @param $handler callback 该函数支持三个参数,根据参数顺序是
	 * 1. error message
	 * 2. error code
	 * 3. additional info , sql etc.
	 */
	public function setErrorHandler($handler){
		$old_handler = $this->errhandler;
		if(is_callable($handler)){
			$this->errhandler = $handler;
			return $old_handler;
		}
		return false;
	}
	/**
	 * @return the $options
	 */
	public function getConnectionOptions() {
		return $this->options;
	}
	/**
	 * 取得当前使用DB的名称
	 * @return string
	 */
	public function getDBName(){
		if($this->config instanceof DBConfiguration){
			return $this->config->database;
		}
		return $this->dbname;
	}
	
	/**
	 * 转义字符串，使之能安全的发送到数据库。
	 * @param $str string 要转义的数据
	 * @return string 转义后的数据
	 */
	public function quote($str){
		if(!get_magic_quotes_gpc()){
			return addslashes($str);
		}
		return $str;
	}
	
	/**
	 * 打开数据库连接。一般不需要调用该函数，查询函数会自动调用该函数
	 */
	public function open(){
		if(isset($this->conn)){
			return;
		}
		$this->conn = $this->getDriver();
		if(is_array($this->config)){
			if($this->options['pconnect'] == 'false' || !$this->options['pconnect']){
				$pconnect = false;
			}else{
				$pconnect = true;
			}
			if(isset($this->dbname[0])){
				$dbname = $this->dbname;
			}else{
				$dbname = $this->config['dbname'];
			}
			$this->conn->connect($this->config['host'],
			$this->config['username'],$this->config['password'],
			$dbname,$pconnect,
			$this->options['charset'],$this->options['newlink']);
		}elseif($this->config instanceof DBConfiguration){
			$this->conn->connect($this->config->host,
			$this->config->username,$this->config->password,
			$this->config->database,$this->config->pconnect,
			$this->config->charset,$this->config->newlink);
		}
	}
	/**
	 * 关闭数据库连接
	 */
	public function close(){
		if($this->conn){
			$this->conn->close();
			$this->conn = null;
		}
	}
	/**
	 * 改变当前的数据库
	 * @param $dbname
	 * @return bool
	 */
	public function changeDB($dbname){
		if($this->config instanceof DBConfiguration){
			$this->config->database = $dbname;
		}else{
			$this->dbname = $dbname;
		}
		return $this->getConnection()->selectDB($dbname);
	}
	
	/**
	 * 设置数据库的设置
	 * @param $config mixed a pear style dsn or an array
	 * a dsn is like driver://username:password@localhost/dbname?option=a
	 * current support driver 'mysql','mssql','mssqlnt'
	 */
	public function setConfig($config){
		if(is_string($config) ){
			$this->dsn = $config;
			$this->parseDsn();
		}elseif(is_array($config)){
			$this->config = new DBConfiguration($config);
		}elseif($config instanceof DBConfiguration){
			$this->config = $config;
		}
	}
	/**
	 * 获取数据库的配置信息
	 */
	public function getConfig(){
		return $this->config;
	}
	/**
	 * 是executeNonQuery的别名
	 * @param $sql
	 * @param $params
	 * @return int
	 */
	public function execute($sql,$params = null){
		 $this->executeNonQuery($sql,$params);
		 return $this->getConnection()->affectedRows();
	}
	/**
	 * 开始一个事务
	 * @return bool
	 */
	public function startTransaction(){
		$this->open();
		return $this->conn->query('START TRANSACTION');
	}
	/**
	 * 提交事务
	 * @return bool
	 */
	public function commit(){
		$this->open();
		return $this->conn->query('COMMIT');
	}
	/**
	 * 回滚事务
	 * @return bool
	 */
	public function rollback(){
		$this->open();
		return $this->conn->query('ROLLBACK');
	}
	
	/**
	 * 执行SQL
	 * @param $sql string
	 * @param $params mixed
	 * @return mixed
	 */
	public function query($sql,$params = null){
		$sql = $this->getSQL($sql,$params);
		return $this->getConnection()->query($sql);
	}
	/**
	 * 执行update,insert,delete等非查询的SQL，返回受影响的行数。
	 * @param $sql string
	 * @param $params mixed
	 * @return int 受影响的行数
	 *
	 */
	public function executeNonQuery($sql,$params = null){
		$sql = $this->getSQL($sql,$params);
		return $this->getConnection()->query($sql);
	}
	/**
	 * 获取执行executeNonQuery之后影响的行数
	 * @return int
	 */
	public function affectedRows(){
		return $this->getConnection()->affectedRows();
	}
	/**
	 * 获取查询的所有结果行。
	 * @param $sql string
	 * @param $params mixed
	 * @return array 查询的结果
	 */
	public function fetchAll($sql,$params = null){
		$sql = $this->getSQL($sql,$params);
		$rs = $this->getConnection()->getAll($sql);
		return $rs;
	}
	/**
	 * 获取SQL结果中某一列的值数组
	 * @param $sql
	 * @param $column_name
	 * @param $params
	 * @param $nocache
	 * @return array
	 */
	public function getColumnAll($sql,$column_name,$params = null,$nocache = false){
		$sql = $this->getSQL($sql,$params);
		return $this->getConnection()->getColumn($sql,$column_name,$nocache);
	}
	
	/**
	 * 获取查询的所有结果行。
	 * @param $sql string
	 * @param $params mixed
	 * @return array 查询的结果
	 */
	public function getAll($sql,$params = null,$nocache = false){
		return $this->fetchAll($sql,$params,$nocache);
	}
	/**
	 * 根据指定的字段，从查询结果中取得一个pair关联数组
	 * @param string $sql
	 * @param string $key_field 用作数组key的字段
	 * @param string $value_field 用作value的字段
	 * @param array $params 查询使用的参数
	 * @return array
	 */
	public function getPairs($sql,$key_field,$value_field,$params = null){
		$sql = $this->getSQL($sql,$params);
		return $this->getConnection()->getPairs($sql,$key_field,$value_field);
	}
	/**
	 * 将查询的结果通过object数组的形式返回
	 * @param $sql
	 * @param $class_name 用于返回的类名
	 * @param $params sql查询参数
	 * @param $obj_args 用于创建对象的参数
	 * @return array
	 */
	public function getObjectAll($sql,$class_name = 'stdClass',$params = null,$obj_args = null){
		$sql = $this->getSQL($sql,$params);
		return $this->getConnection()->getObjectAll($sql,$class_name,$obj_args);
	}
	
	/**
	 * 获取全部数据,结果是以数据行中指定的字段为key的关联数组
	 *
	 * @param string $sql
	 * @param string $key 作为key的字段
	 * @param array $params
	 * @return array
	 */
	public function getAssocAll($sql,$key,$params = null,$nocache = false){
		$sql = $this->getSQL($sql,$params);
		return $this->getConnection()->getAssocAll($sql,$key,$nocache);
	}
	/**
	 * 获取全部数据,组成以数据行中指定的字段和分界字符的字符串
	 *
	 * @param string $sql
	 * @param string $key 作为key的字段
	 * @param array $params
	 * @param string $delimiter
	 * @return string
	 */
	public function getAndJoinAll($sql,$key,$params = null,$nocache = false,$delimiter = ','){
		$sql = $this->getSQL($sql,$params);
		return $this->getConnection()->getAndJoinAll($sql,$key,$delimiter,$nocache);
	}
	/**
	 * 获取执行错误的错误代码
	 * @return int
	 */
	public function getCode(){
		return $this->getConnection()->getErrorCode();
	}
	/**
	 * 获取执行错误的错误消息
	 * @return string
	 */
	public function getMessage(){
		return $this->getConnection()->getErrorMessage();
	}
	
	/**
	 * 获取查询结果中特定行数。
	 * @param $sql string
	 * @param $params mixed
	 * @return array 查询结果
	 */
	public function fetchLimit($sql,$params = null,$limit = 0,$start=0){
		$sql = $this->getSQL($sql,$params);
		if($limit > 0){
			if(stripos($sql,'limit') === false){
				$sql .= sprintf(" LIMIT %d,%d",intval($start),intval($limit));
			}
		}
		return $this->getConnection()->getAll($sql);
	}
	/**
	 * 或者查询结果中的第一行。
	 * @param $sql string
	 * @param $params mixed
	 * @return array 查询结果的第一行
	 */
	public function fetchOne($sql,$params = null){
		$sql = $this->getSQL($sql,$params);
		return $this->getConnection()->getOne($sql);
	}
	
	/**
	 * 或者查询结果中的第一行。
	 * @param $sql string
	 * @param $params mixed
	 * @return array 查询结果的第一行
	 */
	public function getOne($sql,$params = null){
		return $this->fetchOne($sql,$params);
	}

	/**
	 * 从表中取得一条数据
	 * @param $tablename 表名
	 * @param $wheresqlarr 数组指定的where条件
	 * @return mixed
	 */
	public function selectOne($tablename, $wheresqlarr,$silent = 0) {
		$where = $comma = '';
		if(empty($wheresqlarr)) {
			$where = '1';
		} elseif(is_array($wheresqlarr)) {
			foreach ($wheresqlarr as $key => $value) {
				$where .= $comma.'`'.$key.'`'.'=\''.$value.'\'';
				$comma = ' AND ';
			}
		} else {
			$where = $wheresqlarr;
		}
		return $this->getConnection()->getOne('select * from '.$tablename.' WHERE '.$where);
	}
	/**
	 * 根据查询获取一个向量值。如果查询中指定了多列，则返回第一列的值。
	 * @param $sql string
	 * @param $params mixed
	 * @return object 查询结果的向量值
	 */
	public function fetchScalar($sql,$params = null){
		$sql = $this->getSQL($sql,$params);
		$this->open();
		$resource= $this->conn->query($sql);
		return $this->conn->result($resource,0);
	}
	
	public function resultFirst($sql,$params = null){
		return $this->fetchScalar($sql,$params);
	}
	
	public function fetchAssoc($sql){
		$sql = $this->getSQL($sql);
		$this->open();
		$resource= $this->conn->query($sql);
		return $this->conn->fetchArray($resource);
	}
	
	/**
	 * 根据特定条件查询一个表
	 * @param $sql string
	 * @param $params mixed
	 * @return array 查询结果
	 */
	public function fetchTable($tablename,$wheresqlarr,$columns = null){
		if(empty($wheresqlarr)) {
			$where = '1=1';
		} elseif(is_array($wheresqlarr)) {
			$where = $this->joinPairs($wheresqlarr,' AND ');
		} else {
			$where = $wheresqlarr;
		}
		if(empty($columns)){
			$columns = '*';
		}
		return $this->fetchAll("SELECT $columns FROM $tablename WHERE " . $where);
	}
	/**
	 * 根据特定条件更新表。
	 * @param $tablename string 表名称
	 * @param $setsqlarr array 更新的数据字段为key，内容为value
	 * @param $wheresqlarr array where条件的数组
	 * @param $silent boolean
	 */
	public function updatetable($tablename, $setsqlarr, $wheresqlarr, $silent=0) {
		$setsql = $this->joinPairs($setsqlarr);
		if(empty($wheresqlarr)) {
			$where = '1=1';
		} elseif(is_array($wheresqlarr)) {
			$where = $this->joinPairs($wheresqlarr,' AND ');
		} else {
			$where = $wheresqlarr;
		}
		$sql = 'UPDATE '. $tablename.' SET '.$setsql.' WHERE '.$where;
		$re = $this->query($sql, $silent?'SILENT':'');
		if($re){
			return $this->getConnection()->affectedRows();
		}
		return $re;
	}
	/**
	 * 往表中插入一条数据
	 * @param $tablename string 表名称
	 * @param $insertsqlarr array 插入的数据的字段为key，内容为value
	 * @param $returnid boolean 使用返回插入的id
	 * @param $replace boolean 是否使用replace
	 * @param $silent boolean
	 */
	public function inserttable($tablename, $insertsqlarr, $returnid = 0, $replace = false, $silent = 0) {
		$insertkeysql = $insertvaluesql = $comma = '';
		foreach ( $insertsqlarr as $insert_key => $insert_value ) {
			$insertkeysql .= $comma . '`' . $insert_key . '`';
			$insertvaluesql .= $comma . '\'' . $insert_value . '\'';
			$comma = ', ';
		}
		
		$method = $replace ? 'REPLACE' : 'INSERT';
		$this->getConnection()->query ( $method . ' INTO ' . $tablename . ' ('
		. $insertkeysql . ') VALUES (' . $insertvaluesql . ') ',
		 $silent ? 'SILENT' : '' );
		if ($returnid && ! $replace) {
			$insert_id = $this->conn->insertId ();
		}
		else{
			$insert_id = false;
		}
		return $insert_id;
	}

	public static function joinPairs($pair_arr,$delemeter = ','){
		if(empty($pair_arr)){
			return '';
		}
		$str = '';
		$comma = '';
		foreach ($pair_arr as $key => $value) {
			$str .= $comma .'`' . $key . '` =';
			if(is_int($value)){
				$str .= $value;
			}
			else{
				$str .= '\'' . addslashes($value) . '\'';
			}
			$comma = $delemeter;
		}
		return $str;
	}
	/**
	 * 获取内部使用的连接
	 * @return MooMySQL
	 */
	public function getConnection(){
		if(!isset($this->conn)){
			$this->open();
		}
		return $this->conn;
	}
	
	protected function getDriver(){
		if(is_array($this->config)){
			$driver = $this->config['driver'];
		}elseif($this->config instanceof DBConfiguration){
			$driver = $this->config->driver;
		}
		// current support mysql and mssql only, so ignore the driver setting
		switch ($driver) {
			case 'mssql':
				require_once FRAMEWORK .'/database/drivers/MooMsSQL.class.php';
				$instance = new MooMsSQL();
			break;
			case 'mssqlnt':
				require_once FRAMEWORK .'/database/drivers/MsSQLnt.class.php';
				$instance = new MsSQLnt();
				break;
			default:
				require_once FRAMEWORK .'/database/drivers/MooMySQL.class.php';
				$instance = new MooMySQL();
			break;
		}
		return $instance;
	}
	/**
	 * 将以$prefix开始的字符串替换为config中定义的数据库表前缀
	 *
	 * @param string $sql 要执行的SQL文
	 * @param string $prefix 数据库表前缀替换符，默认是"#__"
	 * @return 替换以后的SQL文
	 */
	protected function replacePrefix( $sql, $prefix='#__' )
	{
		if(strpos($sql, $prefix) === false){
			return $sql;
		}
		$sql = trim( $sql );

		$escaped = false;
		$quoteChar = '';

		$n = strlen( $sql );

		$startPos = 0;
		$literal = '';
		while ($startPos < $n) {
			$ip = strpos($sql, $prefix, $startPos);
			if ($ip === false) {
				break;
			}

			$j = strpos( $sql, "'", $startPos );
			$k = strpos( $sql, '"', $startPos );
			if (($k !== FALSE) && (($k < $j) || ($j === FALSE))) {
				$quoteChar	= '"';
				$j			= $k;
			} else {
				$quoteChar	= "'";
			}

			if ($j === false) {
				$j = $n;
			}

			$literal .= str_replace( $prefix, $this->table_prefix,substr( $sql, $startPos, $j - $startPos ) );
			$startPos = $j;

			$j = $startPos + 1;

			if ($j >= $n) {
				break;
			}

			// quote comes first, find end of quote
			while (TRUE) {
				$k = strpos( $sql, $quoteChar, $j );
				$escaped = false;
				if ($k === false) {
					break;
				}
				$l = $k - 1;
				while ($l >= 0 && $sql{$l} == '\\') {
					$l--;
					$escaped = !$escaped;
				}
				if ($escaped) {
					$j	= $k+1;
					continue;
				}
				break;
			}
			if ($k === FALSE) {
				// error in the query - no end quote; ignore it
				break;
			}
			$literal .= substr( $sql, $startPos, $k - $startPos + 1 );
			$startPos = $k+1;
		}
		if ($startPos < $n) {
			$literal .= substr( $sql, $startPos, $n - $startPos );
		}
		return $literal;
	}
	
	protected function getSQL($sql,$params = null){
		if(is_array($params)){
			$sql = vsprintf($sql,$params);
		}
		elseif(!is_null($params)){
			$sql = sprintf($sql,$params);
		}
		$sql = $this->replacePrefix($sql);
		return $sql;
	}
	/**
	 * 根据dsn解析出数据库的配置。
	 * dsn的格式是：driver://username:password@host/database?option=opt_value
	 */
	protected function parseDsn(){
		if(empty($this->dsn)){
			return false;
		}
		$dsn = parse_url($this->dsn);
		if(empty($dsn)){
			return false;
		}
		$this->config['driver'] = strtolower($dsn['scheme']);
		$this->config['host'] = $dsn['host'];
		if(!empty($dsn['port'])){
			$this->config['host'] .= ':' . intval($dsn['port']);
		}
		$this->config['password'] = $dsn['pass'];
		$this->config['username'] = $dsn['user'];
		if(isset($dsn['path'][1])){
			$this->dbname = substr($dsn['path'],1);
			$this->config['database'] = $this->dbname;
		}
		$this->options = array('charset' => 'utf8',
			'newlink' => false,
			'pconnect' => false);
		if(isset($dsn['query'])){
			parse_str($dsn['query'],$opt);
			$this->options = array_merge($this->options,$opt);
			if(isset($opt['table_prefix'])){
				$this->table_prefix = $opt['table_prefix'];
			}
		}
		return true;
	}
}
