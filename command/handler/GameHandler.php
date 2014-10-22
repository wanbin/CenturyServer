<?php
require_once PATH_CACHE . 'GameCache.php';
class GameHandler extends GameCache{
	
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
	
	public function getLikeInfo($id){
		return parent::getLikeInfo($id);
	}
	
}