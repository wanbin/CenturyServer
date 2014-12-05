<?php
/**
 * @author WanBin @date 2013-12-30
 * 惩罚与真心话
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'PunishModel.php';
class PunishCache extends PunishModel{
	
	

	
	/*
	 * 返回第n页的内容 @see PublishModel::getPage()
	 */
	protected function getPage($page) {
		$listNeedChange = "publish_list_1";
		$count = $this->getListLen ( $listNeedChange );
		$randStart = rand ( 0, $count - PAGECOUNT );
		if ($page > 1) {
		//	$randStart = $page * PAGECOUNT;
		}
		$list = $this->getListRange ( $listNeedChange, $randStart, $randStart+PAGECOUNT );
		$ret = array ();
		foreach ( $list as $key => $value ) {
			$tem = $this->getPunish ( $value );
			if(empty($tem)){
				$this->removeList($listNeedChange, $value);
			}
			$ret []=$tem;
		}
		return $ret;
	}
	
	protected function updatePunish($id,$content){
		parent::updatePunish($id,$content);
		$key = $this->getPunishKey ( $id );
		return $this->delFromCache ( $key );
	}
		
	/**
	 * 审核词汇
	 * @param unknown_type $id
	 * @param unknown_type $type
	 */
	public function changeShow($id,$type){
			// 把待审核的词汇放到正常列表中
		$listNeedChange = "publish_list_0";
		$listNeedChange1 = "publish_list_1";
		$listNeedChange2 = "publish_list_2";
		$listNeedChange3 = "publish_list_3";
		$listTo = "publish_list_" . $type;
		$this->removeList($listNeedChange, $id);
		$this->removeList($listNeedChange1, $id);
		$this->removeList($listNeedChange2, $id);
		$this->removeList($listNeedChange3, $id);
		$this->pushListLeft($listTo, $id);
		return parent::changeShow ( $id,$type );
	}
		
		/*
	 * 返回第n页的内容 @see PublishModel::getPage()
	 */
	protected function getPageShenHe($page) {
		$listNeedChange = "publish_list_0";
		$list = $this->getListRange ( $listNeedChange, 0, 30 );
		$ret = array ();
		foreach ( $list as $key => $value ) {
			$tem = $this->getPunish ( $value );
			if(empty($tem)){
				$this->removeList($listNeedChange, $value);
			}
			$ret[]=$tem;
		}
		return $ret;
	}
	
	
	protected function getRandomOne($type){
		$listKey = "publish_list_1";
		$listCount = $this->getListLen ( $listKey );
		if($listCount<=50){
			return $this->getPunishFromText();
// 			return array();
		}
		$randindex = rand ( 0, $listCount - 1 );
		$punishid=$this->getListValueByIndex($listKey,$randindex);
		return $this->getPunish($punishid);
	}
	

	
	public function getPunishFromText(){
		global $chengfa;
		return array("content"=>$chengfa[array_rand($chengfa)]);
	}
	
	protected function getPunish($id) {
		$key = $this->getPunishKey ( $id );
		$ret = $this->getFromCache ( $key );
		if (empty ( $ret )) {
			$ret = parent::getPunish ( $id );
			if (! empty ( $ret )) {
				$this->setToCache ( $key, $ret );
			}
		}
		return $ret;
	}
	
	public function newPublish($message, $type) {
		$listNeedChange = "publish_list_0";
		$id = parent::newPublish ( $message, $type );
		$this->pushListLeft ($listNeedChange, $id );
		return $id;
	}
	
	
	private function getPunishKey($id) {
		return sprintf ( CACHE_KEY_PUBLISH, $id );
	}
	
}