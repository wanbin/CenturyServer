<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户建筑类，此类进行操作细化
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_CACHE . 'WordsCache.php';
class GuessHandler extends WordsCache {
	public function __construct($uid,$channel='ANDROID') {
		$this->tableName='guess';
		parent::__construct($uid,$channel);
	}
	
	/**
	 * 添加一个新闻公告
	 */
	public function newWords($words, $type) {
		return parent::newWords($words, $type);
	}
	public function updateWords($id,$words, $type) {
		return parent::updateWords($id,$words, $type);
	}
	public function delWords($id){
		return parent::delWords($id);
	}
	
	public function getRandomOne($type) {
		$ret = parent::getRandomOne ($type);
		return $ret;
	}
	public function getPage($page, $type) {
		$ret = parent::getPage ( $page, $type );
		return $ret;
	}
	
	public function getTypeList(){
		return array(
				array('value'=>1,'content'=>'大明星'),
				array('value'=>2,'content'=>'网络达人'),
				array('value'=>3,'content'=>'表演'),
				array('value'=>4,'content'=>'动物'),
				array('value'=>5,'content'=>'日常用品'),
				array('value'=>6,'content'=>'8090'),
				);
	}
}