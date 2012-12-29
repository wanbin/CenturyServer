<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户建筑类，此类进行操作细化
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_CACHE . 'BuildingCache.php';
class BuildingHandler extends BuildingCache{
	/**
	 *
	 * @var RuntimeHandler
	 */
	protected $runtimeObject = Null;
	protected $buildingArr = array (
			1 => 'crop',
			2 => 'stone'
	);
	public function upgrade($id) {
		$buildingInfo = $this->getOneFromCache ();
		// check if is upgradeing
		$runtimeInfo = $this->runtimeObject->get ();
		if (array_key_exists ( $id, $runtimeInfo ['upgrade'] )) {
			$this->throwException ( 'This task is in progress.', 254 );
		}
		$costTime = 100;
		$this->runtimeObject->addUpgrade ( $id, $buildingInfo [$this->getTypeFromId ( $id )], time (), $costTime );
		return $costTime;
	}
/**
 * @param unknown_type $id
 * @return array
 */
	
	public function getCost($id) {
		$buildingInfo = $this->getOneFromCache ();
		$currentLevel = $buildingInfo [$this->getTypeFromId ( $id )];
		return array (
				'crop' => 100
		);
	}
	public function complate($id) {
		$buildingInfo = $this->getOneFromCache ();
		// check if is in upgradeing
		$runtimeInfo = $this->runtimeObject->get ();
		if (! array_key_exists ( $id, $runtimeInfo ['upgrade'] )) {
			$this->throwException ( 'This task is not in progress.', 254 );
		}
		$this->runtimeObject->removeUpgrade ( $id );
		$buildingType = $this->getTypeFromId ( $id );
		$nowLevel = $buildingInfo [$id];
		$this->update ( array (
				$buildingType => $nowLevel ++
		) );
		return $nowLevel;
	}
	
	protected function getRuntimeInfo() {
		if (empty ( $this->runtimeObject )) {
			require_once PATH_HANDLER . 'RuntimeHandler.php';
			$this->runtimeObject = new RuntimeHandler ( $this->gameuid );
		}
		return $this->runtimeObject;
	}
	protected function getTypeFromId($id) {
		return $this->buildingArr [$id];
	}
	protected function getIDFromType($type) {
		$ret = array_flip ( $this->buildingArr );
		return $ret [$type];
	}
}