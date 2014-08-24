<?php
/**
 * @author WanBin @date 2014-02-26
 * 谁是卧底词汇
 */
require_once PATH_MODEL.'BaseModel.php';
class GameModel extends BaseModel {
	protected function addLikeDislike($gameid, $type='like') {
		$content=array(
				'gameuid'=>$this->gameuid,
				'gameid'=>$gameid,
				'type'=>$type,
				);
		$id=$this->insertMongo($content, 'game');
		return $id;
	}
	
}