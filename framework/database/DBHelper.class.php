<?php
require_once  FRAMEWORK . '/database/ConnectionManager.class.php';
/**
 * 用于访问数据库的辅助类
 * @author shusl
 *
 */
class DBHelper{
	private static $config = null;
	/**
	 * instance of connectinManager
	 *
	 * @var ConnectionManager
	 */
	private static $conn_manager = null;
	/**
	 * 用于写的db连接
	 *
	 * @var MooMySQL
	 */
	private static $write_conn;
	/**
	 * 是否自动关闭连接
	 *
	 * @var boolean
	 */
	private $autoclose = true;
	/**
	 * 是否处在事务中，如果处在事务中，则不关闭连接
	 *
	 * @var boolean
	 */
	private $in_trans = false;
	public function __construct($config,$autoclose = true){
		if(self::$config == null){
			self::$config = $config;
		}
		if(self::$conn_manager == null){
			self::$conn_manager = new ConnectionManager($config);
		}else{
			self::$conn_manager->setConfig($config);
		}
		$this->autoclose = $autoclose;
	}
	
	public function setConfig($config){
		self::$config = $config;
		self::$conn_manager->setConfig($config);
	}
	
	public function close(){
		if(!$this->in_trans){
			self::$conn_manager->closeAll();
			self::$write_conn = null;
		}
	}
	
	public function getOne($sql,$params = null){
		$db = $this->getReadConn();
		$sql = $this->getSQL($sql,$params);
		$rs = $db->getOne($sql);
		return $rs;
	}
	
	public function getAll($sql,$params = null,$nocache = false){
		$db = $this->getReadConn();
		$sql = $this->getSQL($sql,$params);
		$rs = $db->getAll($sql,$nocache);
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
		$db = $this->getReadConn();
		$sql = $this->getSQL($sql,$params);
		return $db->getColumn($sql,$column_name,$nocache);
	}
	
	public function getAssocAll($sql,$key,$params = null,$nocache = false){
		$db = $this->getReadConn();
		$sql = $this->getSQL($sql,$params);
		$rs = $db->getAssocAll($sql,$key,$nocache);
		return $rs;
	}
	
	public function getAndJoinAll($sql,$key,$params = null,$nocache = false,$delimiter = ','){
		$db = $this->getReadConn();
		$sql = $this->getSQL($sql,$params);
		$rs = $db->getAndJoinAll($sql,$key,$delimiter,$nocache);
		return $rs;
	}
	
	public function resultFirst($sql,$params = null){
		$db = $this->getReadConn();
		$query = $this->executeSelect($sql,$params);
		$rs = $db->result($query,0);
		return $rs;
	}
	
	public function fetchAll($sql,$params = null){
		return $this->getAll($sql,$params);
	}
	
	public function fetchArray($query){
		$db = $this->getReadConn();
		return $db->fetchArray($query);
	}
	
	/**
	 * 执行一个简单查询
	 *
	 * @param string $tablename
	 * @param mixed $wheresqlarr where条件或者where条件的字段和值的数组
	 * @param string $silent
	 * @return mixed
	 */
	public function select($tablename, $wheresqlarr, $silent=0) {
		$db = $this->getReadConn();
	    //$this->debug('where array:' . print_r($wheresqlarr,true));
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
		$rs = $db->getAll('select * from '.$this->tname($tablename).' WHERE '.$where);
		return $rs;
	}
	
	/**
	 * 查询一条记录
	 *
	 * @param string $tablename
	 * @param mixed $wheresqlarr where条件或者where条件的字段和值的数组
	 * @param string $silent
	 * @return mixed 如果记录存在，返回记录，不存在返回null
	 */
	public function selectOne($tablename, $wheresqlarr, $silent=0){
		$list = $this->select($tablename, $wheresqlarr, $silent);
		if(empty($list)){
			return null;
		}
		return $list[0];
	}
	
	/**
	 * 执行一个简单查询。
	 *
	 * @param string $tablename
	 * @param string $wheresqlarr
	 * @param string $silent
	 * @return mixed
	 */
	public function query($tablename, $wheresqlarr, $silent=0){
		return $this->select($tablename, $wheresqlarr, $silent);
	}
	
	private function setWriteConn(){
		if(self::$write_conn == null){
			self::$write_conn = self::$conn_manager->getWriteConnection();
		}
	}
	/**
	 * 设置是否自动提交
	 *
	 * @param boolean $on 1 开启自动提交，0 关闭自动提交
	 */
	public function setAutoCommit($on = 1){
		$this->setWriteConn();
		if($on){
			self::$write_conn->query('SET AUTOCOMMIT = 1');
		}
		else{
			self::$write_conn->query('SET AUTOCOMMIT = 0');
		}
	}
	/**
	 * 开始一个事务
	 *
	 */
	public function startTransaction(){
		$this->setWriteConn();
		$this->in_trans = true;
		self::$write_conn->query('START TRANSACTION');
	}
	
	/**
	 * 提交一个事务
	 *
	 */
	public function commit(){
		$this->setWriteConn();
		$this->in_trans = false;
		self::$write_conn->query('COMMIT');
	}
	
	/**
	 * 回滚一个事务
	 *
	 */
	public function rollback(){
		$this->setWriteConn();
		$this->in_trans = false;
		self::$write_conn->query('ROLLBACK');
	}
	
	/**
	 * 执行一个SQL
	 *
	 * @param string $sql
	 * @return mixed
	 */
	public function executeSelect($sql,$params = null){
		$db = $this->getReadConn();
		$sql = $this->getSQL($sql,$params);
		return $db->query($sql);
	}
	/**
	 * 执行一个SQL
	 *
	 * @param string $sql
	 * @return mixed
	 */
	public function execute($sql,$params = null){
		$this->setWriteConn();
		$sql = $this->getSQL($sql,$params);
		self::$write_conn->query($sql);
		return self::$write_conn->affectedRows();
	}
	/**
	 * 执行一个insert语句，返回insert的自动增长列的id
	 *
	 * @param string $sql
	 * @param array $params
	 * @return int 自动增长列的插入id
	 */
	public function executeInsert($sql,$params = null){
		$this->setWriteConn();
		$sql = $this->getSQL($sql,$params);
		self::$write_conn->query($sql);
		return self::$write_conn->insertId();
	}
	
	/**
	 * 返回上次插入的id
	 *
	 * @return int
	 */
	public function lastInsertId(){
		$db = self::$conn_manager->getWriteConnection();
		return $db->insertId();
	}
		
	private function autoCloseWrite(){
		if($this->autoclose){
			self::$conn_manager->closeWriteConnection();
		}
	}
	
	private function autoCloseRead(){
		if($this->autoclose){
			self::$conn_manager->closeReadConnection();
		}
	}
	

	private function getSQL($sql,$params = null){
		if(is_array($params)){
			$sql = vsprintf($sql,$params);
		}elseif (!is_null($params)){
			$sql = sprintf($sql,$params);
		}
		$sql = $this->replacePrefix($sql);
// 		$this->debug('sql:' . $sql);
		return $sql;
	}
	/**
	 * 取得用于读的数据库对象
	 *
	 * @return MooMySQL
	 */
	private function getReadConn(){
		return self::$conn_manager->getReadConnection();
	}
	
	public function updatetable($tablename, $setsqlarr, $wheresqlarr, $silent=0) {
		$this->setWriteConn();
		$setsql = $comma = '';
		foreach ($setsqlarr as $set_key => $set_value) {
			$setsql .= $comma.'`'.$set_key.'`'.'=\''.$set_value.'\'';
			$comma = ', ';
		}
		$where = $comma = '';
		if(empty($wheresqlarr)) {
			$where = '1';
		} elseif(is_array($wheresqlarr)) {
			foreach ($wheresqlarr as $key => $value) {
				$where .= $comma.'`'.$key.'`'.'=';
				if(is_int($value)){
					$where .= $value;
				}
				else{
					$where .= '\''.$value.'\'';
				}
				$comma = ' AND ';
			}
		} else {
			$where = $wheresqlarr;
		}
		$sql = 'UPDATE '. $this->tname($tablename).' SET '.$setsql.' WHERE '.$where;
		$this->debug($sql);
		$re = self::$write_conn->query($sql, $silent?'SILENT':'');
		if($re){
			return self::$write_conn->affectedRows();
		}
		return $re;
	}
	
	public function inserttable($tablename, $insertsqlarr, $returnid = 0, $replace = false, $silent = 0) {
		$this->setWriteConn();
		$insertkeysql = $insertvaluesql = $comma = '';
		foreach ( $insertsqlarr as $insert_key => $insert_value ) {
			$insertkeysql .= $comma . '`' . $insert_key . '`';
			$insertvaluesql .= $comma . '\'' . $insert_value . '\'';
			$comma = ', ';
		}
		$method = $replace ? 'REPLACE' : 'INSERT';
		self::$write_conn->query ( $method . ' INTO ' . $this->tname ( $tablename ) . ' (' . $insertkeysql . ') VALUES (' . $insertvaluesql . ') ', $silent ? 'SILENT' : '' );
		if ($returnid && ! $replace) {
			$insert_id = self::$write_conn->insertId ();
		}
		else{
			$insert_id = false;
		}
		return $insert_id;
	}
	
	/**
	 * 将以$prefix开始的字符串替换为config中定义的数据库表前缀
	 *
	 * @param string $sql 要执行的SQL文
	 * @param string $prefix 数据库表前缀替换符，默认是"#__"
	 * @return 替换以后的SQL文
	 */
	public static function replacePrefix( $sql, $prefix='#__' )
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

			$literal .= str_replace( $prefix, self::$config['dbprefix'],substr( $sql, $startPos, $j - $startPos ) );
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
	
	public static function tname($tablename){
		return self::$config['dbprefix'] . $tablename;
	}
	
	public function debug($var){
	    if(defined('FDEBUG') && constant('FDEBUG') == true){
	    	//$backtraces = debug_backtrace();
	    	$msg = "[". date('Y-m-d H:i:s') .'][debug]';
//	    	$msg .= "\r\nCall trace:\r\n";
//	    	foreach ($backtraces as $key => $backtrace) {
//	    		$msg .= sprintf("[%d]\r\n\t file:%s\r\n \t line:%s\r\n \t class:%s\r\n \t function:%s\r\n ",
//	    		$key,basename($backtrace['file']),$backtrace['line'],$backtrace['class'],$backtrace['function']);
//	    	}
	    	$msg .= print_r($var,true) . "\r\n";
	    	if(!file_exists('log')){
	    		mkdir('log');
	    	}
	    	$file = 'log/sql_debug_'. date('Y-m-d') .'.log';
	        file_put_contents($file,$msg,FILE_APPEND);
	    }
	}
}
?>
