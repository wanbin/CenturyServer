<?php
/**
 * @author WanBin @date 2013-08-03
 * 微信用户表
 */
require_once PATH_MODEL.'BaseModel.php';
class WXModel extends BaseModel {
	
	protected function Log($content) {
		$insert = array (
				'gameuid' => $this->gameuid,
				'content' => $content 
		);
		return $this->insertMongo ( $insert, 'wx_log' );
	}
	
	protected function getReturnFromMongo($keyword) {
		$where = array (
				'keyword' => $keyword
		);
		$ret = $this->getOneFromMongo( $where, 'wx_return', 'century_admin' );
		return $ret['content'];
	}
	protected function delReturn($keyword) {
		$where = array (
				'keyword' => $keyword 
		);
		$ret = $this->removeMongo ( $where, 'wx_return', 'century_admin' );
		return $ret;
	}
	protected function updateReturn($keyword,$content) {
		$where = array (
				'keyword' => $keyword 
		);
		$content = array (
				'keyword' => $keyword,
				'content' => $content 
		);
		$id = $this->updateMongo ( $content, $where, 'wx_return', 'century_admin' );
		return $id;
	}
	protected function newReturn($keyword, $content) {
		$content=array(
				'keyword'=>$keyword,
				'content'=>$content,
		);
		$id=$this->insertMongo($content, 'wx_return','century_admin');
		return $id;
	}
	protected function getReturnList() {
		$ret = $this->getFromMongo ( array (), 'wx_return', array (
				"_id" => - 1 
		), 0, 100, 'century_admin' );
		return $ret;
	}
}