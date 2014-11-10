<?php
/**
 * 玩家房间基本类
 */
require_once PATH_CACHE . 'RankCache.php';
class RankHandler extends RankCache{
	public function getPeoplecount($gametype, $level) {
		$ret = $this->getchuanguancount ( $level );
		$result=array();
		foreach ($ret as $key=>$value){
			$result['level'.$key]=array (
						'peoplecount' => $value 
				);
		}
		return $result;
	}
	
	public function changguan($level,$name) {
		return parent::changguan ( $level);
	}
	
	
}