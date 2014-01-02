<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户建筑类，此类进行操作细化
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_CACHE . 'PublishCache.php';
class PublishHandler extends PublishCache{
	
	/**
	 * 添加一个新闻公告
	 */
	public function newPublish($message,$type){
		$content=array(
				'content'=>$message,
				'time'=>time(),
				'type'=>$type,
				'isshow'=>0,
				);
		$this->add($content);
	}
	public function getPage($page) {
		return parent::getPage ( $page );
	}
	
	public function addLike($id, $type) {
		if ($type == 1) {
			parent::addLike ( $id, 1, 0 );
		} else {
			parent::addLike ( $id, 0, 1 );
		}
	}
}