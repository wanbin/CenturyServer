<?php
/**
 * @author WanBin @date 2014-02-26
 * 谁是卧底词汇
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'ArticleModel.php';
class ArticleCache extends ArticleModel {
	
	/**
	 * 喜欢游戏
	 *
	 * @param unknown_type $id        	
	 */
	protected function like($id) {
		$likeKey = $this->getGameLikeGameKey ( $id );
		if (!$this->isExit($likeKey, $this->gameuid)) {
			$this->setRedisHash ( $likeKey,$this->gameuid,time());
			parent::addLikeDislike ( $id, 'like' );
		}
		return $this->getHashLen ($likeKey);
	}
	
	/**
	 * 不喜欢游戏
	 *
	 * @param unknown_type $id        	
	 */
	protected function dislike($id) {
		$dislikeKey = $this->getGameDisLikeGameKey ( $id );
		if (! $this->isExit( $dislikeKey, $this->gameuid )) {
			$this->setRedisHash( $dislikeKey,$this->gameuid,time());
			parent::addLikeDislike ( $id, 'dislike' );
		}
		return $this->getHashLen ($dislikeKey);
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
	
	
	
	protected function getGameList($page,$type){
		$key="Game_list_".$type."_".$page;
		$ret=$this->getFromCache($key);
		if(empty($ret)){
			$ret=parent::getGameList($page,$type);
			$this->setToCache($key, $ret,10);
		}
		return $ret;
	}
	
	public function getGameLast(){
		$key="Game_last";
		$ret=$this->getFromCache($key);
		if (empty ( $ret )) {
			$tem = $this->getGameList ( 1 );
			$ret = $tem [0];
			$this->setToCache ( $key, $ret, 60 );
		}
		return $ret;
	}
	
	protected function getOne($id){
		$key="Game_id_".$id;
		$ret=$this->getFromCache($key);
		if(empty($ret)){
			$ret=parent::getOne($id);
			$this->setToCache($key, $ret,10);
		}
		return $ret;
// 		return parent::getOne($id);
	}
	
}