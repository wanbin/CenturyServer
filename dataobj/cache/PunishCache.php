<?php
/**
 * @author WanBin @date 2013-12-30
 * 惩罚与真心话
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'PunishModel.php';
class PunishCache extends PunishModel{
	
	// contenttype 0,全部 1，真心话 2，大冒险，3看演技
	
	/*
	 * 返回第n页的内容 @see PublishModel::getPage()
	 */
	protected function getPage($page, $showtype, $contenttype) {
		if ($showtype != 1) {
			return parent::getPagePunish ( $page, $contenttype, $showtype );
		}
		$key = "Punish_List_" . $page . "_" . $contenttype;
		$ret = $this->getFromCache ( $key );
		if (empty ( $ret )) {
			$ret = parent::getPagePunish ( $page, $contenttype, $showtype );
			$this->setToCache ( $key, $ret, 2 );
		}
		return $ret;
	}
	
	protected function updatePunish($id,$content,$contenttype){
		parent::updatePunish($id,$content,$contenttype);
		$key = $this->getPunishKey ( $id );
		return $this->delFromCache ( $key );
	}
	protected function delPunish($id){
		parent::delPunish($id);
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
// 		$listNeedChange = "publish_list_0";
// 		$listNeedChange1 = "publish_list_1";
// 		$listNeedChange2 = "publish_list_2";
// 		$listNeedChange3 = "publish_list_3";
// 		$listTo = "publish_list_" . $type;
// 		$this->removeList($listNeedChange, $id);
// 		$this->removeList($listNeedChange1, $id);
// 		$this->removeList($listNeedChange2, $id);
// 		$this->removeList($listNeedChange3, $id);
// 		$this->pushListLeft($listTo, $id);
		return parent::changeShow ( $id,$type );
	}
		
	protected function getRandomOne($type) {
		$randIndex = rand ( 0, 1000 );
		$key = "Punish_random_" . $type . "_" . $randIndex;
		$ret = $this->getFromCache ( $key );
		if (empty ( $ret )||true) {
			$ret=parent::getRandomOne ( $type );
			$this->setToCache($key, $ret,20);
		}
		return $ret;
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
				$this->setToCache ( $key, $ret, 3600 );
			}
		}
		return $ret;
	}
	
	public function newPublish($message, $type) {
// 		$listNeedChange = "publish_list_0";
		$id = parent::newPublish ( $message, $type );
// 		$this->pushListLeft ($listNeedChange, $id );
		return $id;
	}
	
	
	private function getPunishKey($id) {
		return sprintf ( CACHE_KEY_PUBLISH, $id );
	}
	
}