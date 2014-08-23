<?php
/**
 * @author WanBin @date 2014-02-26
 * 谁是卧底词汇
 */
require_once PATH_MODEL.'BaseModel.php';
class GameModel extends BaseModel {
	protected function addLikeDislike($gameid, $type='like') {
		$newid=$this->getIdNew("game");
		$content=array(
				'_id'=>$newid,
				'gameuid'=>$this->gameuid,
				'gameid'=>$gameid,
				'type'=>$type,
				);
		$monogdb = $this->getMongdb ();
		$collection = $monogdb->selectCollection('game');
		$ret = $collection->insert ( $content );
		return $content ['_id'];
	}
	
}