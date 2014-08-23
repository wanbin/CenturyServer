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
		$fromRedis = new Rediska_Key_Hash ( $this->getGameLikeGameKey ( $id ) );
		if (! $fromRedis->exists ( $this->gameuid )) {
			$fromRedis [$this->gameuid] = time ();
			parent::addLikeDislike ( $id, 'like' );
		}
		return $fromRedis->count ();
	}
	
	/**
	 * 不喜欢游戏
	 *
	 * @param unknown_type $id        	
	 */
	protected function dislike($id) {
		$fromRedis = new Rediska_Key_Hash ( $this->getGameDisLikeGameKey ( $id ) );
		if (! $fromRedis->exists ( $this->gameuid )) {
			$fromRedis [$this->gameuid] = time ();
			parent::addLikeDislike ( $id, 'dislike' );
		}
		return $fromRedis->count ();
	}
	public function getLikeInfo($id) {
		$fromlike = new Rediska_Key_Hash ( $this->getGameLikeGameKey ( $id ) );
		$fromdislike = new Rediska_Key_Hash ( $this->getGameDisLikeGameKey ( $id ) );
		return array (
				'likecount' => $fromlike->count (),
				'dislikecount' => $fromdislike->count (),
				'isliked'=>$fromlike->exists($this->gameuid),
				'isdisliked'=>$fromdislike->exists($this->gameuid),
		);
	}
	private function getGameLikeGameKey($gameid) {
		return sprintf ( REDIS_KEY_GAMELIKE_GAME, $gameid );
	}
	private function getGameDisLikeGameKey($gameid) {
		return sprintf ( REDIS_KEY_GAMEDISLIKE_GAME, $gameid );
	}
}