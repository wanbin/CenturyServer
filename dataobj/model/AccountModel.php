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
    
    
    protected function updateUserName($name,$photo){
    	$content=array('username'=>$name,'photo'=>$photo);
    	$where=array("_id"=>intval($this->gameuid) );
    	$this->updateMongo($content, $where, 'users');
    	return true;
    }

    protected function accountLogin(){
    	//把用户的不可推送标记去除，更新用户这次登录时间
    	$this->updateMongo(array('$unset'=>array('push_error'=>''),'logintime'=>time()), array('uid'=>$uid), 'users','centurywar');
    	
    }

}