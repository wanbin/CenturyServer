<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户建筑表
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'BuildingModel.php';
class BuildingCache extends BuildingModel{
	private $item = array ();
	
	/**
	 * 得到一条记录
	 *
	 * @param $id unknown_type
	 * @return Ambigous <boolean, multitype:, multitype:multitype: >
	 */
	protected function getOneFromCache() {
		$key = $this->getCacheKey ();
		$ret = $this->getFromCache ( $key, $this->gameuid );
		if (empty ( $ret )) {
			$ret = parent::getOne ();
			if (! empty ( $ret )) {
				$this->setToCache ( $key, $ret, 0, $this->gameuid );
			}
		}
		return $ret;
	}
	
	/**
	 * 更新信息
	 *
	 * @param $content unknown_type
	 * @return Ambigous <boolean, number, multitype:>
	 */
	
	protected function updateOne($templateid, $content) {
		parent::update ( $templateid, $content );
		return $this->delFromCacheALL ();
	}
	
	/**
	 * 初始化一条信息
	 *
	 * @param $content unknown_type
	 * @return Ambigous <boolean, number, multitype:>
	 */
	protected function init() {
		return	parent::init($this->gameuid);
	}
	
	protected function delFromCache() {
		$key =  $this->getCacheKey ();
		return $this->delToCache ($key,$this->gameuid);
	}
	
	private function getCacheKey() {
		return sprintf ( MEMCACHE_KEY_BUILDING,$this->gameuid  );
	}
	
}