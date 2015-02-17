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
	
	
	/**
	 * 这个返回唯一不重复的谁是卧底词汇或其它词汇
	 * @param unknown_type $type
	 * @param unknown_type $remove 这是从列表上删除相应的词汇，如果疯狂猜词就没必要删除30条了
	 */
	protected function getUniqueOne($type, $remove) {
		// 用户的redis列表
		$keyUserList = $this->tableName . "_user_self_key_" . $this->gameuid;
		// 用户最新的一条信息id,更新的时候使用
		$keyUserListId = $this->tableName . "_user_self_key_maxid_" . $this->gameuid;
		
		$hash = new Rediska_Key_Hash ( $keyUserList );
		if ($hash->count () == 0) {
			$this->setToCache ( $keyUserListId, 0 );
			$this->reBuildUniqueList ();
		}
		$wordsList = $hash->getFieldsAndValues ();
		if ($type > 0) {
			$wordsListType = array ();
			foreach ( $wordsList as $id => $wordstype ) {
				if ($wordstype == $type) {
					$wordsListType [$id] = $wordstype;
				}
			}
			if (! empty ( $wordsListType )) {
				$wordsList = $wordsListType;
			}
		}
		$randid = array_rand ( $wordsList );
		if ($remove) {
			$hash->remove ( $randid );
		}
		return $this->getOne ( $randid );
	}
	
	
	protected function getUniqueList() {
		// 用户的redis列表
		$keyUserList = $this->tableName . "_user_self_key_" . $this->gameuid;
		// 用户最新的一条信息id,更新的时候使用
		$hash = new Rediska_Key_Hash ( $keyUserList );
		if ($hash->count () == 0) {
			$keyUserListId = $this->tableName . "_user_self_key_maxid_" . $this->gameuid;
			$this->setToCache ( $keyUserListId, 0 );
		}
		$this->reBuildUniqueList ();
		
		$ret = $hash->getValues ();
		return array_flip ( array_flip ( $ret ) );
	}
	
	
	protected function getOne($id){
		$catchkey=$this->tableName."_one_key_".$id;
		$ret=$this->getFromCache($catchkey);
		if(empty($ret)){
			$ret= parent::getOne($id);
			$this->setToCache($catchkey, $ret);
		}
		return $ret;
	}
	
	
	public function updateWords($id, $words, $type) {
		return parent::updateWords($id, $words, $type);
	}
	
	protected function delWords($id){
		return parent::delWords($id);
	}
	
	/**
	 * 在用户登录的时候，或是在词汇为空的情况下，rebuild
	 * @return number
	 */
	protected function reBuildUniqueList() {
		// 先看看用户最大ID是多少
		$keyUserListId = $this->tableName . "_user_self_key_maxid_" . $this->gameuid;
		$fromid = $this->getFromCache ( $keyUserListId );
		// 缓存里面有没有这个最大ID的内容，设置1天1刷新
		$keyMoreThan = $this->tableName . "_content_more_than_" . $fromid;
		$ret = $this->getFromCache ( $keyMoreThan );
		if (empty ( $ret )||true) {
			$ret = $this->getListById ( $fromid );
			$this->setToCache ( $keyMoreThan, $ret, 86400 );
		}
		if(empty($ret)){
			return 0;
		}
		$keyUserList = $this->tableName . "_user_self_key_" . $this->gameuid;
		$hash = new Rediska_Key_Hash ( $keyUserList );
		$count = 0;
		foreach ( $ret as $key => $value ) {
			if ($value ['_id'] > $fromid) {
				$fromid = $value ['_id'];
			}
			$hash->set ( $value ['_id'], $value ['type'] );
			$count ++;
		}
		$this->setToCache ( $keyUserListId, $fromid );
		return $count;
	}
}