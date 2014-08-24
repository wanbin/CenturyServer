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
	public function getAccountByGameuid($gameuid) {
		$where=array('_id' => intval ( $gameuid ) );
		$ret = $this->getOneFromMongo ( $where, 'users' );
		return $ret;
	}
    
    
    protected function updateUserName($name){
    	$content=array('username'=>$name);
    	$where=array("_id"=>intval($this->gameuid) );
    	$this->updateMongo($content, $where, 'users');
    	return true;
    }



}