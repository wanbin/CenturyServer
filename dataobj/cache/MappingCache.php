<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户Mapping映射表
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_DATAOBJ . 'MappingModel.php';
class MappingCache extends MappingModel{
	private $item = array ();
		
	/**
	 * 得到所有记录
	 */
	protected function get($uidArr = array()) {
		$hasNoGameuid = array_diff ( $this->item, $uidArr );
		if (! empty ( $hasNoGameuid )) {
			$ret = parent::get ( $hasNoGameuid );
			if (! empty ( $ret )) {
				$this->item = array_merge ( $this->item, $ret );
			}
		}
		return $this->item;
	}
	
	/**
	 * 得到一条记录
	 *
	 * @param $id unknown_type
	 * @return Ambigous <boolean, multitype:, multitype:multitype: >
	 */
	public function getOne() {
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
	
	
	public function getOneByUid($uid) {
		$key = $this->getUidCacheKey ($uid);
		$ret = $this->getFromCache ( $key, $this->gameuid );
		if (empty ( $ret )) {
			$ret = parent::getOneUid($uid);
			if (! empty ( $ret )) {
				$this->setToCache ( $key, $ret, 0, $this->gameuid );
			}
		}
		return $ret;
	}
		
	/**
	 * 添加一条信息
	 *
	 * @param $content unknown_type
	 * @return Ambigous <boolean, number, multitype:>
	 */
	protected function add($content) {
		parent::add ( $content );
		return $this->setToCache ( $this->getCacheKey (), $content, 0, $this->gameuid );
	}
	
	public function addOne($uid) {
		$this->get ();
		parent::add ( $uid );
		$this->item [$this->gameuid] = $uid;
		$key = $this->getCacheKeyAll ();
		return $this->setToCache ( $key, $this->item, 0, $this->gameuid );
	}
	
	public function addarr($content) {
		$this->get ();
		parent::addarr ( $content );
		foreach ( $content as $key => $vlaue ) {
			$this->item [$vlaue ['templateid']] = $vlaue;
		}
		$key = $this->getCacheKeyAll ();
		return $this->setToCache ( $key, $this->item, 0, $this->gameuid );
	}
	/**
	 * 删除一条信息
	 *
	 * @param $id unknown_type
	 * @return number
	 */
	protected function del() {
		parent::del ();
		return $this->delFromCache ();
	}
	
	protected function delOne($templateid) {
		$this->get ();
		parent::delOne ( $templateid );
		if (true) {
			unset ( $this->item [$templateid] );
		} else {
			foreach ( $this->item as $key => $value ) {
				if ($value ['templateid'] == $templateid) {
					unset ( $this->item [$key] );
					break;
				}
			}
		}
		$key = $this->getCacheKeyAll ();
		return $this->setToCache ( $key, $this->item, 0, $this->gameuid );
	}
	
	protected function delFromCache() {
		$key =  $this->getCacheKey ();
		return $this->delToCache ($key,$this->gameuid);
	}
	
	protected function delFromCacheALL() {
		return $this->delToCache ( $this->getCacheKeyAll (), $this->gameuid );
	}
	
	private function getCacheKey() {
		return sprintf ( MEMCACHE_KEY_MAPPING,$this->server,$this->gameuid  );
	}
	
	private function getUidCacheKey($uid) {
		return sprintf ( MEMCACHE_KEY_MAPPING_UID,$this->server,$uid );
	}
	
	private function getCacheKeyAll() {
		return sprintf ( MEMCACHE_KEY_MAPPING_ALL,$this->server, $this->gameuid );
	}
}