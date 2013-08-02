<?php
include_once 'BaseCommand.php';
include_once PATH_CACHE . 'BuildingCache.php';
class UpgradeComplete extends BaseCommand {
	protected function executeEx($params) {
		$buildingId = $params ['id'];
		if (! isset ( $buildingId ) || empty ( $buildingId )) {
			$this->licitException ( 'building id is empty', 112 );
		}
		$buildingHD = $this->createBuildingHD ( $this->gameuid );
		$newlevel = $buildingHD->complate ( $buildingId );
		return array (
				'level' => $newlevel,
		);
	}
}