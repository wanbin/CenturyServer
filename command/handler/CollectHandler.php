<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户建筑类，此类进行操作细化
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_CACHE . 'CollectCache.php';
/**
 * @author wanhin
 *
 */
class CollectHandler extends CollectCache{
	
	/**
	 * 喜欢游戏
	 * @param unknown_type $id
	 */
	public function like($id){
		return parent::like($id);
	}
	/**
	 * 不喜欢游戏
	 * @param unknown_type $id
	 */
	public function dislike($id){
		return parent::dislike($id);
	}
	
	
	/**
	 *通过id数组返回相应的状态
	 */
	public function getAllByIds($idarray){
		return parent::getAllByIds($idarray);
	}
	public function getUserListList(){
		return parent::getUserListList();
	}
}