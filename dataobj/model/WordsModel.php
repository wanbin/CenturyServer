<?php
/**
 * @author WanBin @date 2014-02-26
 * 谁是卧底词汇
 */
require_once PATH_MODEL.'BaseModel.php';
class WordsModel extends BaseModel {
	protected function newWords($words,  $type) {
		if(empty(trim ( $words))||empty($type)){
			return;
		}
		$content = array (
				'content' => trim ( $words),
				'type' => intval($type),
				'status' => 0,
				'time' => time ()
		);
		$id=$this->insertMongo($content, "words");
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
		return $this->updateMongo ( $content, $where, 'words' );
	}
	
	protected function delWords($id){
		$where = array (
				"_id" => intval ( $id )
		);
		return $this->removeMongo( $where, 'words' );
	}
	
	protected function getRandomOne($type = 0) {
		// 先取到count
		$where = array (
				'type' => intval ( $type ) 
		);
		if ($type == 0) {
			$where = array ();
		}
		
		$count = $this->getMongoCount ( $where, 'words' );
		$ret = $this->getFromMongo ( $where, 'words', array (), rand ( 1, $count ) - 1, 1 );
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
		$ret = $this->getFromMongo ( $where, 'words', array ('_id'=>-1), ($page - 1) * PAGECOUNT, PAGECOUNT );
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
}