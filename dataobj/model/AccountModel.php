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
    
    /**
     +----------------------------------------------------------
     * 获取表名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getTableName()
    {
        return 'user_account';
    }
    public function getTableWeixin(){
    	return 'wx_account';
    }
    
    protected function getFields() {
		return 'gameuid,server,uid,exp,level,createtime,updatetime,power,ip,country,authcode,points';
	}
	
	/**
	 * +----------------------------------------------------------
	 * 获取用户全部信息
	 * +----------------------------------------------------------
	 *
	 * @return array +----------------------------------------------------------
	 */
	public function getAccount($gameuid = '') {
		if (empty ( $gameuid )) {
			$gameuid = $this->gameuid;
		}
		$res = $this->hsSelectOne ( $this->getTableName (), $this->getFields (), array (
				'gameuid' => $gameuid
		) );
		return $res;
	}
	public function getAccountByUid($uid = '') {
		if (empty ( $uid )) {
			$uid = $this->uid;
		}
		$res = $this->hsSelectOne ( $this->getTableWeixin (),"*", array (
				'uid' => $uid
		) );
		return $res;
	}
	
	public function getAccountByGameuid($gameuid = '') {
		if (empty ( $gameuid )) {
			$gameuid = $this->gameuid;
		}
		$res = $this->hsSelectOne ( $this->getTableWeixin (),"*", array (
				'gameuid' => $gameuid
		) );
		return $res;
	}
    
    public function updateAccount($change)
    {
        $res = $this->hsUpdate($this->getTableName(),$change, array('gameuid'=>$this->gameuid));
        return $res;
    }
    




}