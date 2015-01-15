<?php
/**
 * @author WanBin @date 2014-02-26
 * 谁是卧底词汇
 */
require_once PATH_MODEL.'BaseModel.php';
class LocalModel extends BaseModel {
	protected  $tableName='local';
	protected function create($name, $gameid, $img) {
		$content = array (
				'name' => trim ( $name ),
				'gameid' => intval ( $gameid ),
				'img' =>$img,
				'des' =>'',
				'type' =>0,
				'time' => time () 
		);
		$id = $this->insertMongo ( $content, $this->tableName );
		return $id;
	}
	protected  function update($id,$type,$name, $gameid, $img,$des,$sort) {
		$where = array (
				"_id" => intval ( $id ) 
		);
		$content = array (
				'name' => trim ( $name ),
				'gameid' => intval ( $gameid ),
				'type' => intval ( $type ),
				'img' => $img,
				'des' => $des,
				'sort' => intval ( $sort ),
				'updatetime' => time () 
		);
		return $this->updateMongo ( $content, $where,  $this->tableName );
	}
	
	protected function del($id){
		$where = array (
				"_id" => intval ( $id )
		);
		return $this->removeMongo( $where,  $this->tableName);
	}
	
	protected function getPage($type) {
		$where = array (
				'type' => intval ( $type )
		);
		if ($type == 0) {
			$where = array ();
		}
		$ret = $this->getFromMongo ( $where, $this->tableName, array (
				'sort' => - 1 
		));
		$result = array ();
		foreach ( $ret as $key => $value ) {
			$result [] = array (
					'_id' => $value ['_id'],
					'name' => $value ['name'],
					'gameid' => $value ['gameid'],
					'img' => $value ['img'],
					'des' => $value ['des'],
					'type' => $value ['type'],
					'sort' => $value ['sort'],
			);
		}
		return $result;
	}
}