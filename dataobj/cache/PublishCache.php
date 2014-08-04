<?php
/**
 * @author WanBin @date 2013-12-30
 * 惩罚与真心话
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'PublishModel.php';
class PublishCache extends PublishModel{
	private $item = array ();
	
	/*
	 * 返回第n页的内容 @see PublishModel::getPage()
	 */
	protected function getPage($page) {
		$count = $this->getCount ();
		return parent::getPage ( rand ( 1, $count-PAGECOUNT ) );
	}
		
	/**
	 * 审核词汇
	 * @param unknown_type $id
	 * @param unknown_type $type
	 */
	public function changeShow($id,$type){
		return parent::changeShow ( $id,$type );
	}
	
	/*
	 * 返回第n页的内容 @see PublishModel::getPage()
	 */
	protected function getPageShenHe($page) {
		return parent::getPageShenHe ( $page );
	}

	
}