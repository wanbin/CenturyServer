<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: Mysql.Class.php 2008-3-19 aming$
*/


!defined('IN_MOOPHP') && exit('Access Denied');


class MooMySQL {
	var $queryCount = 0;
	var $conn;
	var $result;
	var $rsType = MYSQL_ASSOC;
	//note:查询时间
	var $queryTimes = 0;
	
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
	function connect($dbHost = '', $dbUser = '', $dbPass = '', $dbName = '', $dbOpenType = false ,$dbCharset = 'utf8') {
		if($dbOpenType) {
			if(!$this->conn = @mysql_pconnect($dbHost, $dbUser, $dbPass)) {
				$this->errorMsg('Can not connect to MySQL server');
			}
		} else {
			if(!$this->conn = @mysql_connect($dbHost, $dbUser, $dbPass)) {
				$this->errorMsg('Can not connect to MySQL server');
			}
		}

		$mysqlVersion = $this->getMysqlVersion();

		if($mysqlVersion > '4.1') {
				global $charset, $dbCharset;
				$dbCharset = str_replace('-', '', !$dbCharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? $charset : $dbCharset);
				$serverset = $dbCharset ? 'character_set_connection='.$dbCharset.', character_set_results='.$dbCharset.', character_set_client=binary' : '';
				$serverset .= $mysqlVersion > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && @mysql_query("SET $serverset", $this->conn);
		}

		@mysql_select_db($dbName, $this->conn);
	}
	
	/**
	 * 关闭数据库连接，当您使用持续连接时该功能失效
	 *
	 * @return blooean
	 */
	function close() {
		return mysql_close($this->conn);
	}
	
	/**
	 * 发送查询语句
	 *
	 * @param string $sql
	 * @param string $type
	 * @return blooean
	 */
	function query($sql, $type = "ASSOC") {

		global $debug, $timestamp, $sqldebug, $sqlspenttimes;

		if(MOOPHP_DEBUG) {
			global $_MooPHP;
			$sqlstarttime = $sqlendttime = 0;
			$mtime = explode(' ', microtime());
			$sqlstarttime = number_format(($mtime[1] + $mtime[0] - $_MooPHP['startTime']), 6) * 1000;
		}

		$this->rsType = $type != "ASSOC" ? ($type == "NUM" ? MYSQL_NUM : MYSQL_BOTH) : MYSQL_ASSOC;
		$this->result = @mysql_query($sql, $this->conn);
		$this->queryCount++;

		if(MOOPHP_DEBUG) {
			$mtime = explode(' ', microtime());
			$sqlendttime = number_format(($mtime[1] + $mtime[0] - $_MooPHP['startTime']), 6) * 1000;
			$sqltime = round(($sqlendttime - $sqlstarttime), 3);

			$explain = array();
			$info = mysql_info();
			if(preg_match("/^(select )/i", $sql)) {
				$explain = mysql_fetch_assoc(mysql_query('EXPLAIN '.$sql, $this->conn));
			}
			$_MooPHP['debug_query'][] = array('sql'=>$sql, 'time'=>$sqltime, 'info'=>$info, 'explain'=>$explain);
		}

		if(!$this->result) {
			return $this->errorMsg('MySQL Query Error', $sql);
		} else {
			return $this->result;
		}
	}
	
	/**
	 * 数据量比较大的情况下查询
	 *
	 * @param string $sql
	 * @param string $type
	 * @return blooean
	 */
	function bigQuery($sql, $type = "ASSOC") {
		$this->rsType = $type != "ASSOC" ? ($type == "NUM" ? MYSQL_NUM : MYSQL_BOTH) : MYSQL_ASSOC;
		$this->result = @mysql_unbuffered_query($sql, $this->conn);
		$this->queryCount++;
		if(!$this->result) {
			return $this->errorMsg('MySQL Query Error', $sql);
		}
		else {
			return $this->result;
		}
	}
	
	/**
	 * 获取全部数据
	 *
	 * @param string $sql
	 * @param blooean $nocache
	 * @return array
	 */
	function getAll($sql, $noCache = false) {
		$noCache ? $this->bigQuery($sql) : $this->query($sql);
		$rows = array();
		while($row = mysql_fetch_array($this->result, $this->rsType)) {
			$rows[] = $row;
		}
		return $rows;
	}
	
	/**
	 * 获取单行数据
	 *
	 * @param string $sql
	 * @return array
	 */
	function getOne($sql) {
		$this->query($sql);
		$rows = array();
		$rows = mysql_fetch_array($this->result, $this->rsType);
		return $rows;
	}
	
	/**
	 * 从结果集中取得一行作为关联数组，或数字数组
	 *
	 * @param resource $query
	 * @return array
	 */
	function fetchArray($query) {
		return mysql_fetch_array($query, $this->rsType);
	}

	/**
	 * 取得结果数据
	 *
	 * @param resource $query
	 * @return string
	 */
	function result($query, $row) {
		$query = @mysql_result($query, $row);
		return $query;
	}

	/**
	 * 取得上一步 INSERT 操作产生的 ID 
	 *
	 * @return integer
	 */

	 function insertId() {
		return ($id = mysql_insert_id($this->conn)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}
	
	/**
	 * 取得行的数目
	 *
	 * @param resource $query
	 * @return integer
	 */
	function numRows($query) {
		return mysql_num_rows($query);
	}
	
	/**
	 * 取得结果集中字段的数目
	 *
	 * @param resource $query
	 * @return integer
	 */
	function numFields($query) {
		return mysql_num_fields($query);
	}
	
	/**
	 * 取得前一次 MySQL 操作所影响的记录行数
	 *
	 * 
	 * @return integer
	 */
	function affectedRows() {
		return mysql_affected_rows($this->conn);
	}

	/**
	 * 取得结果中指定字段的字段名 
	 *
	 * @param string $data
	 * @param string $table
	 * @return array
	 */
	function listFields($data, $table) {
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
	function listTables($data) {
		$query = mysql_list_tables($data);
		$rows = array();
		while($row = mysql_fetch_array($query)) {
			$rows[] = $row[0];
		}
		return $rows;
	}
	
	/**
	 * 取得表名
	 *
	 * @param string $table_list
	 * @param integer $i
	 * @return string
	 */
	function tableName($table_list, $i) {
		return mysql_tablename($table_list, $i);
	}
	
	/**
	 * 转义字符串用于查询
	 *
	 * @param string $char
	 * @return string
	 */
	function escapeString($char) {
		return mysql_escape_string($char);
	}
	
	/**
	 * 取得数据库版本信息
	 *
	 * @return string
	 */
	function getMysqlVersion() {
		return mysql_get_server_info();
	}

	/**
	 * 输出错误信息
	 *
	 * @param string $msg
	 * @param string $sql
	 * @return void
	 */
	function errorMsg($msg = '', $sql = '') {
		if($msg) {
			echo "<b>ErrorMsg</b>:".$msg."<br />";
		}
		if($sql) {
			echo "<b>SQL</b>:".htmlspecialchars($sql)."<br />";
		}
		echo "<b>Error</b>:  ".mysql_error()."<br />";
		echo "<b>Errno</b>: ".mysql_errno();
		exit();
	}
}