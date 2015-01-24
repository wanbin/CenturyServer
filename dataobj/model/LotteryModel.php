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
				'gameuid' => intval($this->gameuid)
		);
		return $this->updateMongo ( $content, $where, $this->tablename );
	}
	protected function create(){
		$content = array (
				'gameuid' => intval($this->gameuid),
				'content' => array (
						array (
								'id' =>0,
								'name' => "三等奖",
								'gift' => "三等奖奖品",
								'people' => "3" 
								
						),
						array (
								'id' =>1,
								'name' => "二等奖",
								'gift' => "二等奖奖品",
								'people' => "2" 
						),
						array (
								'id' =>2,
								'name' => "一等奖",
								'gift' => "一等奖奖品",
								'people' => "1" 
						) 
				)
		);
		$this->insertMongo ( $content, $this->tablename );
		return $content;
	}
	protected function getSetting($roomid) {
		$where = array (
				'gameuid' => intval($roomid > 0 ? $roomid : $this->gameuid) 
		);
		$ret = $this->getOneFromMongo ( $where, $this->tablename );
		if (empty ( $ret )) {
			$ret = $this->create ();
		}
		return $ret;
	}
	
	protected function updateShake($isshake){
		$content = array (
				'isshake' => intval($isshake),
				'updatetime' => time ()
		);
		$where = array (
				'gameuid' => intval($this->gameuid)
		);
		$this->updateMongo ( $content, $where, $this->tablename );
		return true;
	}
}