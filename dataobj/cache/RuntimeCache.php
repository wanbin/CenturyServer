<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户正在生产的信息表
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_DATAOBJ . 'RuntimeModel.php';
class RuntimeCache extends RuntimeModel{
	private $item = array ();
	
	/**
	 * 得到一条记录
	 *
	 * @param $id unknown_type
	 * @return Ambigous <boolean, multitype:, multitype:multitype: >
	 */
	protected function getOneFromCache() {
		if (empty ( $this->item )) {
			$key = $this->getCacheKey ();
			$ret = $this->getFromCache ( $key, $this->gameuid );
			if (empty ( $ret )) {
				$ret = parent::get ();
				if (! empty ( $ret )) {
					$this->item = $ret;
					$this->setToCache ( $key, $ret, 0, $this->gameuid );
				}
			}
		}
		return $this->item;
	}
	
	/**
	 * 更新信息
	 *
	 * @param $content unknown_type
	 * @return Ambigous <boolean, number, multitype:>
	 */
	protected function update($content) {
		parent::update ( $content );
		return $this->delFromCache ();
	}
	
	/**
	 * 添加一条信息
	 *
	 * @param $content unknown_type
	 * @return Ambigous <boolean, number, multitype:>
	 */
	protected function init() {
		return parent::init ();
	}
	protected function delFromCache() {
		$key = $this->getCacheKey ();
		return $this->delToCache ( $key, $this->gameuid );
	}
	private function getCacheKey() {
		return sprintf ( MEMCACHE_KEY_RUNTIME, $this->gameuid );
	}
	
}