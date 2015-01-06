<?php
/**
 * @author WanBin @date 2013-12-30
 * 惩罚与真心话
 */
require_once PATH_MODEL.'BaseModel.php';
class PunishModel extends BaseModel {
	/**
	 * 审核词汇
	 *
	 * @param unknown_type $id        	
	 * @param unknown_type $type        	
	 */
	public function changeTypeToInt() {
		$ret = $this->getFromMongo ( array (
				'type' => '1' 
		), 'punish', array (
				"_id" => - 1 
		), 0, 1000 );
		foreach ( $ret as $key => $value ) {
			$where = array (
					"_id" => intval ( $value ['_id'] ) 
			);
			$content = array (
					'type' => intval ( $value ['type'] ) 
			);
		 $this->updateMongo ( $content, $where, 'punish' );
		}
		return count($ret);
	}
	
	protected function getTypeCount($type) {
		// 先取到count
		$where = array (
				'type' => 1,
				'contenttype' => intval ( $type ) 
		);
		if ($type == -1) {
			unset ( $where ['contenttype'] );
		}
		$count = $this->getMongoCount ( $where, 'punish' );
		return $count;
	}
	
	/**
	 * 返回一个随机词汇
	 */
	protected function getRandomOne($type) {
		$count = $this->getTypeCount($type);
		
		// 先取到count
		$where = array (
				'type' => 1,
				'contenttype' => intval ( $type )
		);
		if ($type == -1) {
			unset ( $where ['contenttype'] );
		}
		
		$ret = $this->getFromMongo ( $where, 'punish', array(), rand(1,$count)-1, 1 );
		$result = array ();
		foreach ( $ret as $key => $value ) {
			$result [] = array (
					'_id' => $value ['_id'],
					'content' => $value ['content'],
					'gameuid' => $value ['gameuid'],
					'time' => $value ['time'],
					'contenttype' => $value ['contenttype']
			);
		}
		return $result[0];
	}
	
	
	protected function getPagePunish($page, $contenttype,$showtype){
		if($page<=0){
			$page=1;
		}
		$pagecount = PAGECOUNT;
		$where = array (
				'type' => intval($showtype),
				'contenttype' => intval ( $contenttype ) 
		);
		if ($contenttype == -1) {
			unset($where['contenttype']);
		}
		$ret = $this->getFromMongo ( $where, 'punish', array (
				"updatetime" => - 1 
		), ($page - 1) * $pagecount, $pagecount );
		$result = array ();
		foreach ( $ret as $key => $value ) {
			$result [] = array (
					'_id' => $value ['_id'],
					'content' => $value ['content'],
					'gameuid' => $value ['gameuid'],
					'time' => $value ['time'],
					'type' => $value['type'],
					'contenttype' => $value ['contenttype'] 
			);
		}
		return $result;
	}

	protected function delPunish($id){
		$where = array (
				"_id" => intval ( $id )
		);
		return $this->removeMongo($where, 'punish' );
	}
	
	protected function changeShow($id, $type) {
		$where = array (
				"_id" => intval ( $id ) 
		);
		$content = array (
				'type' => intval ( $type ) ,
				'updatetime'=>time()
		);
		return $this->updateMongo ( $content, $where, 'punish' );
	}
	
	protected function updatePunish($id,$content,$contenttype){
		$where = array (
				"_id" => intval ( $id )
		);
		$content = array (
				'content' => $content,
				'contenttype'=>intval($contenttype),
				'updatetime'=>time()
		);
		return $this->updateMongo ( $content, $where, 'punish' );
	}
	
	protected function getPunish($id) {
		$where = array (
				'_id' => intval ( $id ) 
		);
		return $this->getOneFromMongo ( $where, 'punish' );
	}
	
	public function newPublish($content, $type) {
		$content=array(
				'gameuid'=>$this->gameuid,
				'content'=>$content,
				'contenttype'=>intval($type),
				'type'=>0,
				'updatetime'=>time()
				);
		return $this->insertMongo($content, 'punish');
	}
	
}