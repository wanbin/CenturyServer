<?php
/**
 * @author WanBin @date 2013-12-30
 * 用户收藏表、点赞
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'CollectModel.php';
class CollectCache extends CollectModel{
	public function getAllByIds($idarray) {
		$ret = array ();
		foreach ( $idarray as $key => $value ) {
			$likeKey = $this->getLikeKey ( $value );
			$dislikeKey = $this->getDislikeKey ( $value );
			$tem = array (
					'like' => $this->getHashLen($likeKey),
					'dislike' => $this->getHashLen($dislikeKey),
					'liked' => $this->isExit ( $likeKey, $this->gameuid ),
					'disliked' => $this->isExit ( $dislikeKey, $this->gameuid ),
			);
			$ret [$value] = $tem;
		}
		return $ret;
	}
	
	/**
	 * 喜欢游戏
	 *
	 * @param unknown_type $id        	
	 */
	protected function like($id) {
		$likeKey = $this->getLikeKey ( $id );
		$likeUser = $this->getUserLikeKey ( $this->gameuid );
		$rediska = new Rediska ();
		$list = new Rediska_Key_Hash ( $likeKey );
		$hasDo = $list->exists ( $this->gameuid );
		$list [$this->gameuid] = time ();
		
		$rediska = new Rediska ();
		$list = new Rediska_Key_Hash ( $likeUser );
		$list [$id] = time ();
		// 这一块把之前用户喜欢的内容从mongo取出来
		return array (
				'count' => $this->getHashLen ( $likeKey ),
				'hasdo' => $hasDo 
		);
	}
	
	
	/**
	 * 不喜欢游戏
	 *
	 * @param unknown_type $id
	 */
	protected function dislike($id) {
		$dislikeKey = $this->getDislikeKey ( $id );
		$this->setRedisHash ( $dislikeKey, $this->gameuid, time () );
		$dislikeUser = $this->getUserDislikeKey ( $this->gameuid );
		
		$rediska = new Rediska();
		$list = new Rediska_Key_Hash($dislikeUser);
		$list[$id]=time();
		
//		parent::addLike ( $id, 'dislike' );
		return $this->getHashLen ($dislikeKey);
	}
	protected function getUserListList(){
		$likeUser = $this->getUserLikeKey ( $this->gameuid );
		$rediska = new Rediska();
		$list = new Rediska_Key_Hash($likeUser);
		return $list->getFieldsAndValues();
	}
	
	

	private function getLikeKey($id) {
		return sprintf ( REDIS_KEY_LIKE, $id );
	}
	private function getDislikeKey($id) {
		return sprintf ( REDIS_KEY_DISLIKE, $id );
	}
	private function getUserLikeKey($id) {
		return sprintf ( REDIS_USERKEY_LIKE, $id );
	}
	private function getUserDislikeKey($id) {
		return sprintf ( REDIS_USERKEY_DISLIKE, $id );
	}
	
}