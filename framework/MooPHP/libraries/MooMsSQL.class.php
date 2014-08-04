<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.
	author:五月  QQ:35700324
	$Id: MooMsSQL.Class.php 2008-5-30 sunqingyu$
*/

!defined('IN_MOOPHP') && exit('Access Denied');

class MooMsSQL {
	/**
	 * 连接数据库参数
	 *
	 * @param string $host
	 * @param string $dataBase
	 * @param string $user
	 * @param string $passWord
	 * @param blooean $linkId
	 * @param blooean $queryId
	 * @param integer $row
	 * @param string $errno
	 */
	//var $host = "localhost";
	//var $dataBase = "model";
	//var $user = "sa";
	//var $passWord = "123456";
	var $linkId = 0;
	var $queryId = 0;
	var $row = 0;
	var $errno = 0;
	var $error = "";
	var $affNum = 0;

	/**
	 * 连接数据库，并且选择默认的数据库
	 *
	 * @return void
	 */
	function connect($host = '', $user = '', $passWord = '', $dataBase = '') {
		if ( 0 == $this->linkId ) {
			$this->linkId = mssql_connect($host,$user,$passWord) or die("Couldn't connect to SQL Server on $servername");
			$db=@mssql_select_db($dataBase,$this->linkId);
			if (!$this->linkId) {
				$this->halt("Link-ID == false, mssql_connect failed");
			}
		}
	}

	/**
	 * 打印错误方法：显示页面处理的错误信息
	 *
	 * @param string $msg
	 * @return string
	 */
	function halt($msg) {
		printf("dataBase error: %s\n", $msg);
		printf("mssql error: %s (%s)\n",$this->errno,$this->error);die("Session halted.");
	}

	/**
	 * 关闭数据库，如果数据库连接已经打开则关闭它
	 * 请在调用connect()并处理后使用close()
	 *
	 * @return void
	 */
	function close() {
		if (0 != $this->linkId){
			mssql_close();
		}
	}

	/**
	 * 输入sql语句，有select,update,insert,delete
	 * 包括存储过程也可以通过这个方法来调用。
	 *
	 * @param string $queryString
	 * @return array
	 */
	function query($queryString) {
		$this->queryId = mssql_query($queryString);
		$this->row = 0;
		if (!$this->queryId) {
			$msg=mssql_get_last_message();
			if($msg == null || $msg == ""){
				$this->affNum = 1;
				return 1;
			}

			if(strtolower(substr($queryString,0,6))!="select"){
				$this->affNum = 1;
				return 1;
			}

			$this->errno = 1;
			$this->error = "General error (The mssql interface cannot return detailed error messages)(".$msg.").";
			$this->halt("Invalid SQL: ".$queryString);
		}
	return $this->queryId;
	}

	/**
	 * 把查询数据库的指针移到下一条记录
	 * 
	 * @return array
	 */
	function nextRecord() {
		$this->Record = array();
		mssql_next_result($this->queryId);
		$this->Record=mssql_fetch_array($this->queryId);
		$result = $this->Record;
		if(!is_array($result)) return $this->Record;
		foreach($result as $key => $value){
			$keylower = strtolower($key);
			if($keylower != $key) $this->Record[$keylower] = $value;
		}
	return $this->Record;
	}

	/**
	 * 重新定位查询数据库的指针
	 * 
	 * @return number
	 */
	function seek($pos) {
		if($pos <= 0) return;
		if(eregi("[0-9]",$pos)) mssql_data_seek($this->queryId,$pos);
	}

	/**
	 * 获取查询数据库得到的总行数
	 * 
	 * @return number
	 */
	function numRows() {
		if($this->queryId) $num_rows=mssql_num_rows($this->queryId);
		else $num_rows = $this->affNum;
	return $num_rows;
	}

	/**
	 * 字段数
	 * 
	 * @return number
	 */
	function numFields() {
		return count($this->Record) / 2;
	}

	/**
	 * 该字段的值
	 * 
	 * @return string
	 */
	function fieldValue($Field_Name){
		return $this->Record[$Field_Name];
	}

	/**
	 * update,insert,delete影响的行数
	 * 
	 * @return number
	 */
	function affectedRows() {
		if($this->queryId) return mssql_num_rows($this->queryId);
		else{
			return $this->affNum;
		}
	}
}
