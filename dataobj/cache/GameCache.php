<?php
/**
 * @author WanBin @date 2014-02-26
 * 谁是卧底词汇
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'GameModel.php';
class GameCache extends GameModel {
	
	/**
	 * 喜欢游戏
	 *
	 * @param unknown_type $id        	
	 */
	protected function like($id) {
		$likeKey = $this->getGameLikeGameKey ( $id );
		if (! $this->redis->HEXISTS ( $likeKey, 8)) {
			$this->redis->HMSET ( $likeKey,array($this->gameuid=>time()));
			parent::addLikeDislike ( $id, 'like' );
		}
		return $this->redis->HLEN ($likeKey);
	}
	
	/**
	 * 不喜欢游戏
	 *
	 * @param unknown_type $id        	
	 */
	protected function dislike($id) {
		$dislikeKey = $this->getGameDisLikeGameKey ( $id );
		if (! $this->redis->HEXISTS ( $dislikeKey, $this->gameuid )) {
			$this->redis->HMSET ( $dislikeKey,array($this->gameuid=>time()));
			parent::addLikeDislike ( $id, 'dislike' );
		}
		return $this->redis->HLEN ($dislikeKey);
	}
	public function getLikeInfo($id) {
		$likeKey= $this->getGameLikeGameKey ( $id ) ;
		$dislikeKey= $this->getGameDisLikeGameKey ( $id ) ;
		return array (
				'likecount' => $this->redis->HLEN($likeKey),
				'dislikecount' => $this->redis->HLEN($dislikeKey),
				'isliked'=>$this->redis->HEXISTS($dislikeKey,$this->gameuid),
				'isdisliked'=>$this->redis->HEXISTS($dislikeKey,$this->gameuid),
		);
	}
	private function getGameLikeGameKey($gameid) {
		return sprintf ( REDIS_KEY_GAMELIKE_GAME, $gameid );
	}
	private function getGameDisLikeGameKey($gameid) {
		return sprintf ( REDIS_KEY_GAMEDISLIKE_GAME, $gameid );
	}
}