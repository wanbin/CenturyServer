<?php

require_once (FRAMEWORK .'/database/drivers/MooMsSQL.class.php');

class MsSQLnt extends MooMsSQL {
	
	public function __construct(){
		$this->rsType = SQLSRV_FETCH_ASSOC;
	}
	
	/**
	 * @see MooMsSQL::affectedRows()
	 *
	 * @return number
	 */
	public function affectedRows() {
		return sqlsrv_rows_affected($this->queryId);
	}
	
	/**
	 * @see MooMsSQL::close()
	 *
	 */
	public function close() {
		if($this->linkId){
			sqlsrv_close($this->linkId);
			$this->linkId = false;
		}
	}
	
	public function quote($str){
		if(!get_magic_quotes_gpc()){
			return addslashes($str);
		}
		return $str;
	}
	
	/**
	 * @see MooMsSQL::connect()
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $passWord
	 * @param string $dataBase
	 * @param boolean $pconnect
	 * @param string $charset
	 * @param boolean $newlink
	 */
	public function connect($host, $user = '', $passWord = '', $dataBase = '',
	$pconnect = false,$charset = 'utf8',$newlink = false) {
		if($this->linkId){
			return true;
		}
		$conninfo = array('DATABASE' => $dataBase,
		'UID' => $user,
		'PWD' => $passWord);
		if($pconnect){
			$conninfo['ConnectionPooling'] = 1;
		}
		$this->linkId = sqlsrv_connect($host,$conninfo);
		if(!$this->linkId){
			$this->errorMsg("Connect to '$host' by user '$user' failure.",null,
			'');
		}
		return true;
	}
	public function getAll($sql){
		$res = $this->query($sql);
		$rows = array();
		while ($row = sqlsrv_fetch_array($res,$this->rsType)){
			$rows[] = $row;
		}
		sqlsrv_free_stmt($res);
		return $rows;
	}
	
	public function getOne($sql){
		$res = $this->query($sql);
		$row = sqlsrv_fetch_array($res,$this->rsType);
		sqlsrv_free_stmt($res);
		return $row;
	}
	
	public function fetchArray($query){
		return sqlsrv_fetch_array($query,$this->rsType);
	}
	public function getErrorMsg(){
		return sqlsrv_errors(SQLSRV_ERR_ERRORS);
	}
	public function result($query,$row,$field = 0){
		return sqlsrv_get_field($query,$row);
	}
	
	/**
	 * @see MooMsSQL::nextRecord()
	 *
	 * @return array
	 */
	public function nextRecord() {
		return sqlsrv_fetch_array($this->queryId,$this->rsType);
	}
	
	/**
	 * @see MooMsSQL::numFields()
	 *
	 * @return number
	 */
	public function numFields() {
		return sqlsrv_num_fields($this->queryId);
	}
	
	/**
	 * @see MooMsSQL::numRows()
	 *
	 * @return number
	 */
	public function numRows() {
		return false;
	}
	
	/**
	 * @see MooMsSQL::query()
	 *
	 * @param string $sql
	 * @return array
	 */
	public function query($sql,$params = null) {
		if(!empty($params)){
			$this->queryId = sqlsrv_query($this->linkId,$sql,$params);
		}
		else{
			$this->queryId = sqlsrv_query($this->linkId,$sql);
		}
		if ($this->queryId === FALSE) {
			$this->errorMsg('MSSQL Query Error', $sql,'');
		}
		return $this->queryId;
	}
	
	public function setFetchType($type = "ASSOC"){
		$this->rsType = $type != "ASSOC" ? ($type == "NUM" ? SQLSRV_FETCH_NUMERIC : SQLSRV_FETCH_BOTH) : SQLSRV_FETCH_ASSOC;
	}
	/**
	 * @see MooMsSQL::seek()
	 *
	 * @param int $pos
	 * @return number
	 */
	public function seek($pos) {
		return false;
	}

}

?>