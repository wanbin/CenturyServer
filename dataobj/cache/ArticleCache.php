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
		$userKey=$this->getGameLikeUserKey($this->gameuid);
		$rediska = new Rediska ();
		$list = new Rediska_Key_Hash ( $likeKey );
		$useLike = new Rediska_Key_Hash ( $userKey );
		if ($list->exists ( $this->gameuid )) {
			$list->remove ( $this->gameuid );
			$useLike->remove ( $id);
		} else {
			$list->set ( $this->gameuid, time () );
			$useLike->set ( $id, time () );
		}
		return $list->count();		
	}
	
	/**
	 * 不喜欢游戏
	 *
	 * @param unknown_type $id        	
	 */
	protected function dislike($id) {
		$likeKey = $this->getGameDisLikeGameKey ( $id );
		$userKey=$this->getGameDisLikeUserKey($this->gameuid);
		$rediska = new Rediska();
		$list = new Rediska_Key_Hash($likeKey);
		$list->set($this->gameuid,time());
		$useLike = new Rediska_Key_Hash($likeKey);
		$useLike->set($id,time());
		return $list->count();	
	}
	
	protected function getLikeList(){
		$userKey=$this->getGameLikeUserKey($this->gameuid);
		$rediska = new Rediska();
		$list = new Rediska_Key_Hash($userKey);
		return $list->getFieldsAndValues();
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
	
	private function getGameLikeUserKey($gameid) {
		return sprintf ( REDIS_KEY_GAMELIKE_USER, $gameid );
	}
	private function getGameDisLikeUserKey($gameid) {
		return sprintf ( REDIS_KEY_GAMEDISLIKE_USER, $gameid );
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
			$tem = $this->getGameList ( 1,1 );
			$ret = $tem [0];
			$this->setToCache ( $key, $ret, 60 );
		}
		return $ret;
	}
	protected function getOne($id) {
		$key = "Game_id_" . $id;
		$ret = $this->getFromCache ( $key );
		if (empty ( $ret )) {
			$ret = parent::getOne ( $id );
			$this->setToCache ( $key, $ret, 10 );
		}
		$this->read($id);
		return $ret;
	}
	
	protected function read($id){
		$rediska = new Rediska ();
		// 记录阅读次数及阅读人数
		$readcount = new Rediska_Key_Hash ( "Article_read_count" );
		$readcount->increment ( $id );
		// 记录阅读的人数
		$readcount = new Rediska_Key_Hash ( "Article_read_people_" . $id );
		$readcount->increment ( $this->gameuid );
		// 记录某个玩家阅读的内容
		if ($this->channel != 'WEB') {
			$readcount = new Rediska_Key_Hash ( "Article_people_" . $this->gameuid );
			$readcount->increment ( $id );
		}
	}
	protected function getReadInfo($id) {
		$rediska = new Rediska();
		$readlist = new Rediska_Key_Hash ( "Article_read_count" );
		$readcount = $readlist [$id];
		$peoplelist = new Rediska_Key_Hash ( "Article_read_people_" . $id );
		$readpeople = $peoplelist->count();
		return array (
				'count' => $readcount,
				'people' => $readpeople 
		);
	}
	
	
	protected function getIdFromName($name){
		$key="articel_keyname_".$name;
		$ret=$this->getFromCache($key);
		if(empty($ret)){
			$ret=parent::getIdFromName($name);
			$this->setToCache($key, $ret,60);
		}
		return $ret;
	}
	
}