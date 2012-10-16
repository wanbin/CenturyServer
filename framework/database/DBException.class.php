<?php

class DBException extends Exception {
	protected $sql = null;
	
	public function getSQL(){
		return $this->sql;
	}
	
	public function setSQL($sql){
		$this->sql = $sql;
	}
	
	public function __construct($message, $code,$sql=null) {
		parent::__construct ( $message, $code );
		$this->sql = $sql;
	}
	
	public function __toString(){
		$msg = 'Error Message:' . $this->getMessage() . "\n";
		$msg .= 'Error Code:' . $this->getCode() . "\n";
		if(isset($this->sql{0})){
			$msg .= "SQL:" . $this->sql . "\n";
		}
		//$msg .= $this->getTraceAsString();
		return $msg;
	}
}

?>