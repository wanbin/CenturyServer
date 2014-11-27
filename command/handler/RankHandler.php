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
						'value' => $value 
				);
		}
		return $result;
	}
	
	public function getRankList($gametype,$level){
		$result=array();
		for($i=0;$i<=$level;$i++){
			$rank=$this->getRank($gametype, $i);	
			$result['level'.$i]=array (
					'value' => $rank
			);
		}
		return $result;
	}
	
	public function getRankUser($gametype,$level){
		if ($gametype == 101) {
			$ret = parent::getChuangguanUser ( $level );
		} else {
			$ret = parent::getRankUser ( $gametype, $level );
		}
		$result=array();
		include_once PATH_HANDLER . 'AccountHandler.php';
		$account = new AccountHandler ( $this->uid );
		$rank=1;
		foreach ( $ret as $key => $value ) {
			$userInfo = $account->getAccountByGameuid ( $value['gameuid'] );
			$result[] = array (
					'username' => $userInfo ['username'],
					'rank' => $rank,
					'gameuid' => $key,
					'score' => $value['value']
			);
			$rank ++;
		}
		return $result;
	}

	public function getCell(){
		
	}
	
	public function updateCell($gametype, $level, $data) {
		$count = $this->cellCount ( $gametype, $level, $data );
		return $this->upcateCellSort ( $count );
	}
	
	
	private function cellCount($gametype,$level,$data){
		return $level*$data;	
	}
	
	public function getCellValue(){
		return parent::getCellValue();
	}
	
	
	
	public function getRank($gametype,$level){
		return parent::getRank($gametype, $level);
	}
	public function getRankValue($gametype,$level){
		return parent::getRankValue($gametype, $level);
	}
	
	public function setRank($gametype,$level,$souce){
		return parent::setRank($gametype, $level, $souce);
	}
	
	
	public function changguan($level,$name) {
		return parent::changguan ( $level,$name);
	}
	
	

	
	public function getCellRank() {
		return parent::getCellRank();
	}
	
	
}