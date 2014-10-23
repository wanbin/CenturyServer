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
	public function changeShow($id, $type) {
		$where = array (
				"_id" => intval ( $id ) 
		);
		$content = array (
				'type' => $type 
		);
		return $this->updateMongo ( $content, $where, 'punish' );
	}
	
	protected function updatePunish($id,$content){
		$where = array (
				"_id" => intval ( $id )
		);
		$content = array (
				'content' => $content
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
				'type'=>$type
				);
		return $this->insertMongo($content, 'punish');
	}
	
}