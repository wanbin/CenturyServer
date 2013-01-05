<?php
include_once 'BaseCommand.php';
include_once PATH_CACHE . 'BuildingCache.php';
class Upgrade extends BaseCommand {
	protected function executeEx($params) {
		$buildingId = $params ['id'];
		if (! isset ( $buildingId ) || empty ( $buildingId )) {
			$this->licitException ( 'building id is empty', 112 );
		}
		
		$buildingHD = $this->createBuildingHD ( $this->gameuid );
		
		$change = $buildingHD->getCost ( $buildingId );
		$this->checkUserStatus ( $change );
		
		$costTime = $buildingHD->upgrade ( $buildingId );
		
		$this->updateUserStatus ( $change );
		return array (
				'change' => $change,
				'remainTime' => $costTime
		);
	}
}