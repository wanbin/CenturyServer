<?php
/**
 * @author WanBin @date 2013-08-03
 * 谁是卧底房间信息
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'LotteryModel.php';
class LotteryCache extends LotteryModel{
	protected function isShake($roomid) {
		$shackKey = "Lottery_Shake_" . $roomid;
		$ret = $this->getFromCache ( $shackKey );
		if (empty ( $ret )) {
			$info = parent::getSetting ($roomid);
			$ret = isset ( $info ['isshake'] ) ? $info ['isshake'] : 0;
			$this->setToCache ( $shackKey, $ret );
		}
		return $ret;
	}
// 	protected function updateShake($isshake) {
// 		$shackKey = "Lottery_Shake_" . $this->gameuid;
// 		$this->setToCache ( $shackKey, $isshake );
// 		return parent::updateShake ( $isshake );
// 	}
	
	protected function shake($roomid) {
		$keyList = "Lottery_Redis_Shake_List_" . $roomid;
		$keyCount = "Lottery_Redis_Shake_Count_" . $roomid;
		$rediska = new Rediska ();
		$clicknum=0;
		$clickcount=0;
		if ($this->isShake ( $roomid )) {
			$hash = new Rediska_Key_Hash ( $keyCount );
			$clickcount = $hash->increment ( $this->gameuid );
			$list = new Rediska_Key_List ( $keyList );
			$list->append ( $this->gameuid );
			$clicknum = $list->count ();
		}
		return array (
				'clicknum' => $clicknum,
				'clickcount' => $clickcount 
		);
	}
	
	
	protected function getUserShackCount($userarr){
		$keyCount = "Lottery_Redis_Shake_Count_" . $this->gameuid;
		$rediska = new Rediska ();
		$hash = new Rediska_Key_Hash ( $keyCount );
		$result=array();
		foreach ($userarr as $gameuid){
			$result[$gameuid]=$hash->get($gameuid);
		}
		return $result;
	}
	
	
	protected function updateShake($isshake) {
		$shackKey = "Lottery_Shake_" . $this->gameuid;
		$this->delFromCache ( $shackKey );
		return parent::updateShake ( $isshake );
	}
}