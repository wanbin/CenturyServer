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
	
	/**
	 * 添加一个新游戏
	 * @param unknown_type $title
	 * @param unknown_type $content
	 * @param unknown_type $time
	 */
	
	public function newGame($title,$image,$content,$time){
		return parent::newGame($title,$image,$content,$time);
	}
	
	public function updateGame($id,$title,$image,$content,$time){
		return parent::updateGame($id,$title,$image,$content,$time);
	}
	public function delGame($id){
		return parent::delGame($id);
	}
	
	public function getGameList($page){
		return parent::getGameList($page);
	}
	public function getOne($id){
		return parent::getOne($id);
	}
}