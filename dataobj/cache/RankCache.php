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
		$listkey=$this->getChuangGuanList($level);
		//判断是否是第一个启动的用户
		if($this->getListLen($listkey)==0){
			file_put_contents ( "newlevel$level.log", $this->gameuid, FILE_APPEND );
		}
		$this->pushListLeft($listkey, $this->gameuid."_".time());
		$keycount = $this->getChuangGuanPeople ();
		$this->incrList ( $keycount, $level );
		return $this->getRedisHash ( $keycount, $level );
	}
	
	public function getChuangguanUser($level){
		$listkey=$this->getChuangGuanList($level);
// 		$this->pushListLeft($listkey, $this->gameuid."_".time());
		$ret=$this->getListRange($listkey,0,50);
		$result=array();
		foreach ($ret as $key=>$value){
			$tem=explode("_", $value);
			$result[]=array('gameuid'=>$tem[0],'value'=> date('[m.d] H:i:s',$tem[1]) );
		}
		return $result;
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
	
	protected function getCellRank() {
		$key=$this->getCellKey();
		return $this->getSortRank($key, $this->gameuid)+1;
	}
	
	
	protected function getchuanguancount($level) {
		$keycount = $this->getChuangGuanPeople ();
		return $this->getRedisHashAll($keycount);
	}
	
	
	protected function getRank($gametype,$level) {
		$key=$this->getGameRankKey($gametype,$level);
		//如果是时间的，按越少越胜利
		if($gametype==103){
			$rank=$this->getSortRank($key, $this->gameuid);
		}else{
			$rank=$this->getSortRankLowToHigh($key, $this->gameuid);
		}
		if($rank===false){
			return 0;
		}
		return $rank+1;	
	}
	protected function getRankValue($gametype,$level) {
		$key=$this->getGameRankKey($gametype,$level);
		//如果是时间的，按越少越胜利
		$rank=$this->getSortValue($key, $this->gameuid);
		if($rank===false){
			return 0;
		}
		return $rank;	
	}
	
	
	public function getRankUser($gametype,$level){
		$key=$this->getGameRankKey($gametype,$level);
		//这个是江湖排名
		if($gametype==100){
			$key=$this->getCellKey();
			$rank=$this->getRankString($key,0,50);
		}
		else if($gametype==103){
			$rank=$this->getRankString($key, 0,50);
		}else{
			$rank=$this->getRankStringRev($key, 0,50);
		}
		$result=array();
		foreach ($rank as $key=>$value){
			$result[]=array('gameuid'=>$key,'value'=>$value);
		}
		return $result;
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
	
	private function getChuangGuanList($level) {
		return sprintf ( REDIS_CHUANG_GUAN_LIST, $level );
	}
	
	private function getChuangGuanPeople() {
		return sprintf ( REDIS_CHUANG_GUAN_COUNT);
	}
	
	
	private function getCellKey() {
		return sprintf ( REDIS_CELL_KEY);
	}
	
	
	
}