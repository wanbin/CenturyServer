<?php
/**
 * @author WanBin @date 2014-02-26
 * 谁是卧底词汇
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'PageModel.php';
class PageCache extends PageModel {
	
	protected function getPageList($page){
		$key="Page_list_".$page;
		$ret=$this->getFromCache($key);
		if(empty($ret)||true){
			$ret=parent::getPageList($page);
			$this->setToCache($key, $ret,10);
		}
		return $ret;
	}
	protected function getPageOne($id){
		$key="Page_id_".$id;
		$ret=$this->getFromCache($key);
		if(empty($ret)){
			$ret=parent::getPageOne($id);
			$this->setToCache($key, $ret,10);
		}
		return $ret;
	}
	
	public function updatePage($id,$title,$content){
		$key="Page_id_".$id;
		$this->delFromCache($key);
		return parent::updatePage($id,$title,$content);
	}
	
	public function updateLink($id,$key,$name,$link){
		$linkkey="Page_link_".$key;
		$this->delFromCache($linkkey);
		return parent::updateLink($id,$key,$name,$link);
	}
	
	
	protected function getPageId($key){
		$linkkey="Page_link_".$key;
		$ret=$this->getFromCache($linkkey);
		if(empty($ret)){
			$ret=parent::getPageId($key);
			$this->setToCache($linkkey, $ret,3600);
		}
		return $ret;
	}
	
}