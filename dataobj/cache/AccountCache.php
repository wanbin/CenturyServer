<?php

require_once PATH_MODEL . 'AccountModel.php';
/**
 +----------------------------------------------------------
 *    AccountCache
 +----------------------------------------------------------
 *   获取修改添加用户信息
 *
 +----------------------------------------------------------
 *  @author     Wenson
 *  @version    2012-12-30
 *  @package    dataobj
 +----------------------------------------------------------
 */
class AccountCache extends AccountModel{
    private $account = array();
    
    /**
     +----------------------------------------------------------
     * 获取当前用户的所有信息
     +----------------------------------------------------------
     * @return multitype:
     +----------------------------------------------------------
     */
    public function getAccount($gameuid='')
    {
        if (empty ( $this->account )) {
            $key = $this->getCacheKey($this->gameuid);
            $this->account = $this->getFromCache ( $key, $this->gameuid );
            if (empty ( $this->account )) {
                $this->account = parent::getAccount ($this->gameuid);
                $this->setToCache ( $key, $this->account, 3600, $this->gameuid );
            }
        }
        return $this->account;
    }
    
    
	/**
	 * 取得用户信息
	 * @param unknown_type $uid
	 */
	public function getAccountByUid($uid='') {
		return parent::getAccountByUid ($uid);
	}
	
	public function getUidFromGameuid($gameuid){
		$ret=$this->getAccountByGameuid($gameuid);
		return $ret['uid'];
	}
    
    /**
     +----------------------------------------------------------
     * 获取用户指定字段信息
     +----------------------------------------------------------
     * @param array 字段数组                 array('gameuid','displayname' ...)
     * @return array
     +----------------------------------------------------------
     */
    public function getAccountField($fieldArr)
    {
        $res = array();
        if (empty($this->account))
        {
            $this->getAccount();
        }
        foreach ($fieldArr as $field)
        {
            $res[$field] = $this->account[$field];
        }
        
        return $res;
    }
    
    
    
    /**
     +----------------------------------------------------------
     * 获取多个用户信息
     +----------------------------------------------------------
     * @param array $gameuidArr  用户gameuid数组
     * @return array
     +----------------------------------------------------------
     */
    public function getMultiAccount($gameuidArr)
    {
        $res = array();
        foreach ($gameuidArr as $gameuid)
        {
            $key = $this->getCacheKey($gameuid);
            $account = $this->getFromCache($key, $gameuid);
            if (empty($account))
            {
                $account = parent::getAccount($gameuid);
                $this->setToCache($key, $res, $gameuid);
            }
            $res[$gameuid] = $account;
        }
        Return $res;
    }

    /**
     +----------------------------------------------------------
     * 验证用户是否合法
     +----------------------------------------------------------
     * @param array $param
     * @param array $sign_arr
     * @return array
     +----------------------------------------------------------
     */
    public function validate($param,$sign_arr)
    {
        $authcode = $sign_arr['authcode'];
        $this->getAccount();
        if(empty($this->account))
        {
            $this->throwException('account not exist',StatusCode::ACCOUNT_NOT_EXIST);
        }
        elseif($authcode != $this->account['authcode'])
        {
            $this->throwException('authcode error',StatusCode::AUTH_CODE_ERROR);
        }
        elseif($this->account['is_access'] != 0)
        {
            $this->throwException('you have been spam',StatusCode::ACCOUNT_IS_NO_ACCESS);
        }
        elseif($param)
        {
            	
        }
        return $this->account;
    }
    
    
 /**
     +----------------------------------------------------------
     * 更新账户信息
     +----------------------------------------------------------
     * @param array $change  array('coin'=> -100,'crop'=>100)
     * @return array  更新完account的全部信息
     +----------------------------------------------------------
     */
    public function updateAccount($change)
    {
        //获取需要验证的字段
        $validation = $this->getValidateField();
        
        $account = array();
        //验证
        foreach ($change as $key=>$value)
        {
            if(in_array($key, $validation))
            {
                $account = $this->getAccount();
                $value += $account[$key];
                if($value < 0)
                {
                    $this->throwException('You dont have enough resourse',StatusCode::LESS_RESOURSE);
                }
            }
            $change[$key] = $value;
        }
        
        $res = parent::updateAccount($change);
        if($res)
        {
            $this->account = array_merge($this->account,$change);
            $key = $this->getCacheKey($this->gameuid);
            $this->setToCache($key,$this->account,0,$this->gameuid);
        }
        return $this->account;
    }
    
    
    /**
     +----------------------------------------------------------
     * 获取更新时需要验证的字段
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    private function getValidateField()
    {
        return array('crop','coin','ruby','points');
    }
 
	
	protected function delFromCache() {
		$key =  $this->getCacheKey ($this->gameuid);
		return $this->delToCache ($key,$this->gameuid);
	}
	
	private function getCacheKey($gameuid) {
		return sprintf ( MEMCACHE_KEY_ACCOUNT,$gameuid );
	}

}