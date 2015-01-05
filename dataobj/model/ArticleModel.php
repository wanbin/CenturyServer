<?php
/**
 * @author WanBin @date 2014-02-26
 * 谁是卧底词汇
 */
require_once PATH_MODEL.'BaseModel.php';
class ArticleModel extends BaseModel {
	protected function addLikeDislike($gameid, $type='like') {
		$content=array(
				'gameuid'=>$this->gameuid,
				'gameid'=>$gameid,
				'type'=>$type,
				);
		$id=$this->insertMongo($content, 'game');
		return $id;
	}

	protected function newGame($title,$image,$content,$time,$type){
		$content=array(
				'title'=>$title,
				'homeimg'=>$image,
				'content'=>$content,
				'type'=>intval($type),
				'showtime'=>$time
		);
		$id=$this->insertMongo($content, 'game_content','century_admin');
		return $id;
	}
	
	protected function updateGame($id,$title,$image,$content,$time,$type){
		$where=array('_id'=>intval($id));
		$content=array(
				'title'=>$title,
				'homeimg'=>$image,
				'content'=>$content,
				'type'=>intval($type),
				'showtime'=>$time
		);
		$id=$this->updateMongo($content,$where, 'game_content','century_admin');
		return $id;
	}
	
	protected function getGameList($page,$type){
		$pagecount = 30;
		$where = array (
				'type' => intval($type) ,
				'showtime' => array (
						'$lte' => time () 
				) 
		);
		if ($type == 0) {
			$where = array ();
		}
		$ret = $this->getFromMongo ( $where, 'game_content', array (
				"showtime" => - 1 
		), ($page - 1) * $pagecount, $pagecount, 'century_admin' );
		$result = array ();
		foreach ( $ret as $key => $value ) {
			$result [] = array (
					'_id' => $value ['_id'],
					'title' => $value ['title'],
					'homeimg' => $value ['homeimg'],
					'type' => $value ['type'],
					'showtime' => $value ['showtime'] 
			);
		}
		return $result;
	}
	
	protected function getOne($id){
		$where=array("_id"=>intval($id));
		$ret=$this->getOneFromMongo($where,  'game_content','century_admin');
		return $ret;
	}
	
	public function delGame($id){
		$where=array("_id"=>intval($id));
		$ret=$this->removeMongo($where,  'game_content','century_admin');
		return $ret;
	}
	
}


