<?php
/**
 * @author WanBin @date 2014-02-26
 * 谁是卧底词汇
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'WordsModel.php';
class WordsCache extends WordsModel{
	protected function getRandomOne($type) {
		$ret = parent::getRandomOne ($type);
		return $ret;
	}
	
	protected function getPage($page,$type) {
		$ret = parent::getPage ( $page,$type );
		return $ret;
	}
	
}