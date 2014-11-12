<?php
/**
 * @author WanBin @date 2014-05-21
 * 游戏房间
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'RoomsModel.php';
class RankCache extends RoomsModel{
	
	protected function changguan($level,$name) {
		$key = $this->getChuangGuanKey ( $level );
		if (! $this->isExit ( $key, $this->gameuid )) {
			if ($this->getListLen ( $key ) == 0) {
				// 这个是全新开启的一阶游戏，触发事件
				file_put_contents ( "newlevel$level.log", $this->gameuid, FILE_APPEND );
			}
			$this->pushList ( $key, $this->gameuid );
			$keycount = $this->getChuangGuanPeople ();
			$this->incrList ( $keycount, $level );
		}
		return $this->getRedisHash ( $keycount, $level );
	}
	
	
	/**
	 * 用户的大脑细胞数，用sort排序
	 * @param unknown_type $count
	 */
	protected function upcateCellSort($count){
		$key=$this->getCellKey();
		return $this->incrSortOne($key,$count,$this->gameuid);
	}
	
	protected function getCellValue() {
		$key=$this->getCellKey();
		return $this->getSortValue($key, $this->gameuid);
	}
	
	
	protected function getchuanguancount($level) {
		$keycount = $this->getChuangGuanPeople ();
		return $this->getRedisHashAll($keycount);
	}
	
	
	protected function getRank($gametype,$level) {
		$key=$this->getGameRankKey($gametype,$level);
		//如果是时间的，按越少越胜利
		if($gametype==102){
			$rank=$this->getSortRankLowToHigh($key, $this->gameuid);
		}else{
			$rank=$this->getSortRank($key, $this->gameuid);
		}
		if($rank===false){
			return 0;
		}
		return $rank+1;	
	}
	
	public function setRank($gametype,$level,$souce){
		$key=$this->getGameRankKey($gametype,$level);
		return $this->sortAdd($key,$souce,$this->gameuid);
	}
	
	private function getGameRankKey($gametype,$level) {
		return sprintf ( REDIS_GAME_RANK,$gametype, $level );
	}
	
	
	private function getChuangGuanKey($level) {
		return sprintf ( REDIS_CHUANG_GUAN, $level );
	}
	
	private function getChuangGuanPeople() {
		return sprintf ( REDIS_CHUANG_GUAN_COUNT);
	}
	
	
	private function getCellKey() {
		return sprintf ( REDIS_CELL_KEY);
	}
	
	
	
}