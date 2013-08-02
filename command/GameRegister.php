<?php
include_once 'BaseCommand.php';
class GameRegister extends BaseCommand {
	protected function executeEx($params) {
		return rand (1,10000);
	}
	protected function register() {
		$gameuid = rand ( 1, 10000 );
		$buildingHD = $this->createBuildingHD ( $gameuid );
		$buildingHD->init ();
		$mappingHD = $this->createMappingHD ( $this->server );
		$mappingHD->init ($gameuid,$this->uid);
	}

}