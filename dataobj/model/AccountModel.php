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
    private function getTableName()
    {
        return 'user_account';
    }
    
    protected function getFields() {
		return 'gameuid,server,uid,exp,level,createtime,updatetime,power,ip,country,authcode';
	}
 
    /**
     +----------------------------------------------------------
     * 获取用户全部信息
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function getAccount($gameuid='')
    {
        if (empty($gameuid))
        {
            $gameuid = $this->gameuid;
        }
        $res= $this->hsSelectOne($this->getTableName(), $gameuid, $this->getFields(), array('gameuid'=>$gameuid), '');
        return $res;
    }
    
    public function updateAccount($change)
    {
        $res = $this->hsUpdate($this->getTableName(), $this->gameuid, $change, array('gameuid'=>$this->gameuid));
        return $res;
    }
    



}