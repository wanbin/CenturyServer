<?php
require_once PATH_CACHE . 'ArticleCache.php';
//type 1 游戏 2，帮助
class ArticleHandler extends ArticleCache{
	
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
	
	public function newGame($title,$image,$content,$time,$type=1){
		return parent::newGame($title,$image,$content,$time,$type);
	}
	
	public function updateGame($id,$title,$image,$content,$time,$type=1){
		return parent::updateGame($id,$title,$image,$content,$time,$type);
	}
	public function delGame($id){
		return parent::delGame($id);
	}
	
	public function getGameList($page,$type=0){
		return parent::getGameList($page,$type);
	}
	
	public function getGameLast(){
		return parent::getGameLast();
	}
	
	public function getOne($id){
		return parent::getOne($id);
	}
	
}