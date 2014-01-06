<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户类实例
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_CACHE . 'AccountCache.php';
class AccountHandler extends AccountCache {
	public function getInfo($uid) {
		return parent::getAccountByUid ( $uid );
	}
}