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
		if (! $this->isExit ( $likeKey, $this->gameuid )) {
			$this->setRedisHash ( $likeKey, $this->gameuid, time () );
			parent::addLike ( $id, 'like' );
		} else {
			$this->removeHash ( $likeKey, $this->gameuid, time () );
		}
		return $this->getHashLen($likeKey);
	}
	
	/**
	 * 不喜欢游戏
	 *
	 * @param unknown_type $id
	 */
	protected function dislike($id) {
		$dislikeKey = $this->getDislikeKey ( $id );
		if (! $this->isExit( $dislikeKey, $this->gameuid )) {
			$this->setRedisHash( $dislikeKey,$this->gameuid,time());
			parent::addLike ( $id, 'dislike' );
		}
		else{
			$this->removeHash( $dislikeKey,$this->gameuid,time());
// 			parent::addLike ( $id, 'dislike' );
		}
		return $this->getHashLen ($dislikeKey);
	}
	
// 	public function like($id){
// 		$likeKey = $this->getLikeKey ( $id );
// 		$this->setRedisHash ( $likeKey, $this->gameuid, time () );
// 		return $this->getHashLen ( $likeKey );
// 	}
// 	public function dislike($id){
// 		$dislikeKey = $this->getDislikeKey ( $id );
// 		$this->setRedisHash ( $dislikeKey, $this->gameuid, time () );
// 		return $this->getHashLen ( $dislikeKey );
// 	}
	

	private function getLikeKey($id) {
		return sprintf ( REDIS_KEY_LIKE, $id );
	}
	private function getDislikeKey($id) {
		return sprintf ( REDIS_KEY_DISLIKE, $id );
	}
	
}