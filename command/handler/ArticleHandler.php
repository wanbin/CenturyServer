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
	
	public function getLikeList() {
		$ret = parent::getLikeList ();
		krsort($ret);
		$result = array ();
		foreach ( $ret as $key => $value ) {
			$tem= $this->getOne ( $key );
			unset($tem['content']);
			$result []=$tem;
		}
		return $result;
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
	
	public function getIdFromName($name){
		return parent::getIdFromName($name);
	}
	
	/**
	 * 添加一个新游戏
	 * @param unknown_type $title
	 * @param unknown_type $content
	 * @param unknown_type $time
	 */
	
	public function newGame($title,$image,$content,$time,$type,$keyname){
		return parent::newGame($title,$image,$content,$time,$type,$keyname);
	}
	
	public function updateGame($id,$title,$image,$content,$time,$type,$keyname){
		return parent::updateGame($id,$title,$image,$content,$time,$type,$keyname);
	}
	public function delGame($id){
		return parent::delGame($id);
	}
	
	public function getGameList($page,$type=0){
		$ret= parent::getGameList($page,$type);
		$likeList=parent::getLikeList();
		foreach ($ret as $key=>$value){
			if(key_exists($value['_id'],$likeList)){
				$ret[$key]['hasCollect']=true;
			}else{
				$ret[$key]['hasCollect']=false;
			}
		}
		return $ret;
	}
	
	public function getGameLast(){
		return parent::getGameLast();
	}
	
	public function getOne($id){
		return parent::getOne($id);
	}
	public function getReadInfo($id){
		return parent::getReadInfo($id);
	}
	
}