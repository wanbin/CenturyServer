<?php
/**
 * @author WanBin @date 2014-02-26
 * 谁是卧底词汇
 */
require_once PATH_MODEL.'BaseModel.php';
class WordsModel extends BaseModel {
	protected  $tableName;
	
	protected function newWords($words,  $type) {
		$content = array (
				'content' => trim ( $words),
				'type' => intval($type),
				'status' => 0,
				'time' => time ()
		);
		$id=$this->insertMongo($content, $this->tableName);
		return $id;
	}
	public function updateWords($id, $words, $type) {
		$where = array (
				"_id" => intval ( $id ) 
		);
		$content = array (
				'content' => trim ( $words ),
				'type' => intval ( $type ),
				'updatetime' => time () 
		);
		return $this->updateMongo ( $content, $where,  $this->tableName );
	}
	
	protected function delWords($id){
		$where = array (
				"_id" => intval ( $id )
		);
		return $this->removeMongo( $where,  $this->tableName);
	}
	
	
	protected function getOne($id){
		$where = array (
				"_id" => intval ( $id )
		);
		return $this->getOneFromMongo( $where,  $this->tableName);
	}
	
	
	
	protected function getRandomOne($type) {
		// 先取到count
		$where = array (
				'type' => intval ( $type ) 
		);
		if ($type == 0) {
			$where = array ();
		}
		
		$count = $this->getMongoCount ( $where,  $this->tableName );
		$ret = $this->getFromMongo ( $where,$this->tableName , array (), rand ( 1, $count ) - 1, 1 );
		$result = array ();
		foreach ( $ret as $key => $value ) {
			$result [] = array (
					'_id' => $value ['_id'],
					'type' => $value ['type'],
					'content' => $value ['content'],
			);
		}
		return $result [0];
	}
	
	protected function getPage($page, $type) {
		$where = array (
				'type' => intval ( $type ) 
		);
		if ($type == 0) {
			$where = array ();
		}
		$ret = $this->getFromMongo ( $where,  $this->tableName, array ('_id'=>-1), ($page - 1) * PAGECOUNTADMIN, PAGECOUNTADMIN );
		$result = array ();
		foreach ( $ret as $key => $value ) {
			$result [] = array (
					'_id' => $value ['_id'],
					'type' => $value ['type'],
					'content' => $value ['content'],
			);
		}
		return $result;
	}
	
	/**
	 * 返回ID大于的数
	 * @param unknown_type $id
	 * @return multitype:multitype:unknown  
	 */
	protected function getListById($id) {
		$where = array (
				'_id' => array (
						'$gt' => intval ( $id ) 
				) 
		);
		$ret = $this->getFromMongo ( $where,  $this->tableName, array ('_id'=>1));
		$result = array ();
		foreach ( $ret as $key => $value ) {
			$result [] = array (
					'_id' => $value ['_id'],
					'type' => $value ['type'],
			);
		}
		return $result;
	}
}