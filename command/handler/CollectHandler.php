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
	 * 添加一个新闻公告
	 */
	public function newCollect($id, $type) {
		
		$content = array (
				'gameuid' => $this->gameuid,
				'time' => time (),
				'type' => $type,
				'publish_id' => $id
		);
	
		if ($this->checkCollete ( $id, $type ) > 0) {
			return false;
		}
		include_once 'PublishHandler.php';
		$publish = new PublishHandler ( $this->uid );
		$publish->addLike($id, $type);
		$this->add ( $content );
		return true;
	}
	
	
	public function getPage($page){
		return parent::getPage($page);
	}
	
	/**
	 *通过id数组返回相应的状态
	 */
	public function getAllByIds($idarray){
		return parent::getAllByIds($idarray);
	}
}