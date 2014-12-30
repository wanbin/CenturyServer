<?php
/**
 * @author WanBin @date 2014-02-26
 * 谁是卧底词汇
 */
require_once PATH_MODEL.'BaseModel.php';
class PageModel extends BaseModel {

	protected function newPage($title,$content){
		$content=array(
				'title'=>$title,
				'content'=>$content
		);
		$id=$this->insertMongo($content, 'page_content','century_admin');
		return $id;
	}
	
	protected function updatePage($id,$title,$content){
		$where=array('_id'=>intval($id));
		$content=array(
				'title'=>$title,
				'content'=>$content,
		);
		$id=$this->updateMongo($content,$where, 'page_content','century_admin');
		return $id;
	}
	
	protected function getPageList(){
		$pagecount = 30;
		$ret = $this->getFromMongo ( array (
		), 'page_content', array("_id"=>-1), 0, $pagecount, 'century_admin' );
		$result = array ();
		foreach ( $ret as $key => $value ) {
			$result [] = array('_id'=>$value ['_id'],'title'=>$value ['title'],'time'=>$value['time']);
		}
		return $result;
	}
	

	
	protected function getPageOne($id){
		$where=array("_id"=>intval($id));
		$ret=$this->getOneFromMongo($where,  'page_content','century_admin');
		return $ret;
	}
	
	public function delPage($id){
		$where=array("_id"=>intval($id));
		$ret=$this->removeMongo($where,  'page_content','century_admin');
		return $ret;
	}
	
	
	
	protected function newLink($key,$name,$link){
		$content=array(
				'key'=>$key,
				'name'=>$name,
				'link'=>$link
		);
		$id=$this->insertMongo($content, 'page_link','century_admin');
		return $id;
	}
	
	protected function updateLink($id,$key,$name,$link){
		$where=array('_id'=>intval($id));
		$content=array(
				'key'=>$key,
				'name'=>$name,
				'link'=>$link
		);
		$id=$this->updateMongo($content,$where, 'page_link','century_admin');
		return $id;
	}
	
	public function delLink($id){
		$where=array("_id"=>intval($id));
		$ret=$this->removeMongo($where,  'page_link','century_admin');
		return $ret;
	}
	
	protected function getLinkList(){
		$ret = $this->getFromMongo ( array (
		), 'page_link', array("_id"=>-1), 0, 100, 'century_admin' );
		return $ret;
	}
	
	
	protected function getPageId($key){
		$where=array("key"=>$key);
		$ret=$this->getOneFromMongo($where,  'page_link','century_admin');
		return $ret['link'];
	}
	
}


