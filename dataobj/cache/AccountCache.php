<?php
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *    AccountModel.php
*    Wenson    2012-12-23
*    用户类
*+++++++++++++++++++++++++++++++++++++++++++++++++++++++
*/
include_once PATH_DATAOBJ . 'AccountModel.php';
class AccountCache extends AccountModel
{
    /**
     * 验证用户是否合法
     * @param array $param
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
    
    public function getAccount()
    {
        
    }
}
?>