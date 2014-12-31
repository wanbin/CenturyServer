<?php
require_once PATH_CACHE . 'PageCache.php';
class PageHandler extends PageCache{

	
	/**
	 * 添加一个新游戏
	 * @param unknown_type $title
	 * @param unknown_type $content
	 * @param unknown_type $time
	 */
	
	public function newPage($title,$content){
		return parent::newPage($title,$content);
	}
	
	public function updatePage($id,$title,$content){
		return parent::updatePage($id,$title,$content);
	}
	public function delPage($id){
		return parent::delPage($id);
	}
	
	public function getPageList($page){
		return parent::getPageList($page);
	}
	public function getPageOne($id){
		return parent::getPageOne($id);
	}
	
	/**
	 * 返回相应KEY的界面
	 * 
	 * @param unknown_type $id        	
	 */
	public function getPageFromKey($key) {
		$pageid = parent::getPageId ( $key );
		$ret = $this->getPageOne ( $pageid );
		if (substr ( $key, 0, 2 ) == "WX") {
			if (! empty ( $ret ['content'] )) {
				$ret ['content'] = strip_tags ( $ret ['content'] );
			}
		}
		return $ret;
	}
	
	
	public function getLinkList(){
		return parent::getLinkList();
	}
	
	public function newLink($key,$name,$link){
		return parent::newLink($key,$name,$link);
	}
	
	public function updateLink($id,$key,$name,$link){
		return parent::updateLink($id,$key,$name,$link);
	}
}