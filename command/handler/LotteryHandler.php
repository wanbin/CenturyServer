<?php
require_once PATH_CACHE . 'LotteryCache.php';
class LotteryHandler extends LotteryCache{
	public function updateSetting($content) {
		parent::updateSetting ( $content );
	}
	
	public function getSetting() {
		return parent::getSetting ();
	}
	
	public function shake($roomid) {
		$ret=parent::shake($roomid);
		return $ret;
	}
	
	public function updateShake($isshake){
		return parent::updateShake($isshake);
	}
	
}