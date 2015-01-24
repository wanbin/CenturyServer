<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户建筑类，此类进行操作细化
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_CACHE . 'WordsCache.php';
class WordsHandler extends WordsCache {
	public function __construct($uid,$channel='ANDROID') {
		parent::__construct($uid,$channel);
		$this->tableName='words';
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
	
	public function getUniqueOne($type,$remove=true) {
		$ret = parent::getUniqueOne ($type,$remove);
		return $ret;
	}
	public function getPage($page, $type) {
		$ret = parent::getPage ( $page, $type );
		return $ret;
	}
	
	
	public function getUniqueList(){
		$ret=parent::getUniqueList();
		$tem=$this->getTypeList();
		foreach ($tem as $key=>$value){
			if(!in_array($value['value'], $ret)&&$value['value']!=0){
				unset($tem[$key]);
			}
		}
		return array_values($tem);
	}
	
// 	public function reBuildUniqueList(){
// 		return parent::reBuildUniqueList();
// 	}
	
	
	public function getTypeList(){
		return array(
				array('value'=>0,'content'=>'全部'),
				array('value'=>1,'content'=>'人物'),
				array('value'=>2,'content'=>'日常'),
				array('value'=>3,'content'=>'吃货'),
				array('value'=>4,'content'=>'重口味'),
				array('value'=>5,'content'=>'小清新'),
				array('value'=>6,'content'=>'高难度'),
				);
	}
}