<?php
/**
 * @author WanBin @date 2013-08-03
 * 抽奖类
 */
require_once PATH_MODEL . 'BaseModel.php';
class LotteryModel extends BaseModel {
	private $tablename='lottery';
	protected function updateSetting($content) {
		$content = array (
				'content' => $content,
				'updatetime' => time () 
		);
		$where = array (
				'gameuid' => $this->gameuid 
		);
		$this->updateMongo ( $content, $where, $this->tablename );
	}
	protected function create(){
		$content = array (
				'gameuid' => $this->gameuid,
				'content' => array()
		);
		$this->insertMongo ( $content, $this->tablename );
		return $content;
	}
	protected  function getSetting($roomid = 0) {
		$where = array (
				'gameuid' => $roomid > 0 ? $roomid : $this->gameuid 
		);
		
		$ret = $this->getOneFromMongo ( $where, $this->tablename );
		if (empty ( $ret )) {
			$ret = $this->create ();
		}
		if($ret['content']==null){
			$ret['content']=array();
		}
		return $ret;
	}
	
	protected function updateShake($isshake){
		$content = array (
				'isshake' => (bool)$isshake,
				'updatetime' => time ()
		);
		$where = array (
				'gameuid' => $this->gameuid
		);
		$this->updateMongo ( $content, $where, $this->tablename );
		return true;
	}
}