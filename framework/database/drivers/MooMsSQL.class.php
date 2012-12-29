<?php
require_once FRAMEWORK . '/errors.const.php';

class MooMsSQL {
	protected $linkId = false;
	/**
	 * 是否在出现错误时候抛出exception.
	 * @var boolean
	 */
	protected $throwException = true;
	
	protected $rsType = MSSQL_ASSOC;
	protected $queryId = false;
	
	/**
	 * 错误处理
	 *
	 * @param string $msg
	 * @param string $sql
	 * @return void
	 */
	protected function errorMsg($msg = '', $sql = '',$errno = 1) {
		$message = '';
		if($msg) {
			$message .=  "$msg\n";
		}
		$message .=  "MSSQL Error:  ".$this->getErrorMsg()."\n";
		if($this->throwException){
			require_once FRAMEWORK . '/database/DBException.class.php';
			throw new DBException($message, $errno,$sql);
		}
		else{
			$message .=  "Errno: $errno\n";
			if($sql) {
				$message .=  "SQL:$sql\n";
			}
			trigger_error($message,E_USER_ERROR);
		}
	}
	
	public function quote($str){
		if(!get_magic_quotes_gpc()){
			return addslashes($str);
		}
		return $str;
	}
	
	public function getErrorMsg(){
		return mssql_get_last_message();
	}

	/**
	 * 连接数据库，并且选择默认的数据库
	 *
	 * @return void
	 */
	public function connect($host, $user = '', $passWord = '', $dataBase = '',$pconnect = false,$charset = 'utf8',$newlink = false) {
		if ( false === $this->linkId ) {
			if($pconnect){
				$this->linkId = mssql_pconnect($host,$user,$passWord,$newlink);
			}
			else{
				$this->linkId = mssql_connect($host,$user,$passWord,$newlink);
			}
			if(!$this->linkId){
				$this->errorMsg("Connect to '$host' by user '$user' failure.",null,
				'');
			}
			if($dataBase{0} != '['){
				$dataBase = '[' .$dataBase .']';
			}
			$db = mssql_select_db($dataBase,$this->linkId);
			if ($db === false) {
				$this->errorMsg("Select database $dataBase error",null,
				'');
			}
		}
		return true;
	}

	/**
	 * 关闭数据库，如果数据库连接已经打开则关闭它
	 * 请在调用connect()并处理后使用close()
	 *
	 * @return void
	 */
	public function close() {
		if ($this->linkId){
			mssql_close($this->linkId);
			$this->linkId = false;
		}
	}
	
	public function setFetchType($type = "ASSOC"){
		$this->rsType = $type != "ASSOC" ? ($type == "NUM" ? MSSQL_NUM : MSSQL_BOTH) : MSSQL_ASSOC;
	}
	
	/**
	 * 输入sql语句，有select,update,insert,delete
	 * 包括存储过程也可以通过这个方法来调用。
	 *
	 * @param string $sql
	 * @return array
	 */
 	public function query($sql) {
		$this->queryId = mssql_query($sql,$this->linkId);
		if ($this->queryId === FALSE) {
			$this->errorMsg('MSSQL Query Error', $sql,'');
		}
		return $this->queryId;
	}
	
	public function getAll($sql){
		$res = $this->query($sql);
		$rows = array();
		while ($row = mssql_fetch_array($res,$this->rsType)){
			$rows[] = $row;
		}
		return $rows;
	}
	
	public function getOne($sql){
		$res = $this->query($sql);
		$row = mssql_fetch_array($res,$this->rsType);
		mssql_free_result($res);
		return $row;
	}
	
	public function fetchArray($query){
		return mssql_fetch_row($query);
	}
	
	public function result($query,$row,$field = 0){
		return mssql_result($query,$row,$field);
	}

	/**
	 * 取得上一步 INSERT 操作产生的 ID
	 *
	 * @return integer
	 */

	public function insertId() {
		return false;
	}
	/**
	 * 把查询数据库的指针移到下一条记录
	 *
	 * @return array
	 */
	public function nextRecord() {
		mssql_next_result($this->queryId);
		return mssql_fetch_array($this->queryId);
	}

	/**
	 * 重新定位查询数据库的指针
	 *
	 * @return number
	 */
	public function seek($pos) {
		$pos = intval($pos);
		if($pos >= 0) {
			mssql_data_seek($this->queryId,$pos);
		}
	}

	/**
	 * 获取查询数据库得到的总行数
	 *
	 * @return number
	 */
	public function numRows() {
		return mssql_num_rows($this->queryId);
	}

	/**
	 * 字段数
	 *
	 * @return number
	 */
	public function numFields() {
		return mssql_num_fields ($this->queryId);
	}

	/**
	 * update,insert,delete影响的行数
	 *
	 * @return number
	 */
	public function affectedRows() {
		if($this->queryId){
			return mssql_num_rows($this->queryId);
		}
		else{
			return 0;
		}
	}
}
