<?php
require_once 'BaseModel.php';
/**
 +----------------------------------------------------------
 *    AccountModel
 +----------------------------------------------------------
 *   操作用户的基本信息
 *
 +----------------------------------------------------------
 *  @author     Wenson
 *  @version    2012-12-30
 *  @package    dataobj
 +----------------------------------------------------------
 */
class AccountModel extends BaseModel {
	public function getAccountByUid($uid = '') {
		$sql="select * from wx_account  where uid='$uid'";
		return $this->oneSqlSignle($sql);
	}
	
	public function getAccountByGameuid($gameuid = '') {
		$sql="select * from wx_account  where gameuid=".$this->gameuid;
		return $this->oneSqlSignle($sql);
	}
    
    
    protected function updateUserName($name){
    	$sql="update wx_account set username='$name' where gameuid=".$this->gameuid;
    	return $this->oneSql($sql);
    }



}