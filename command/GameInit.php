<?php
include_once 'GameRegister.php';
class GameInit extends GameRegister {
	protected function executeEx($params) {
		$mappingHD = $this->createMappingHD ( $this->server );
		if (empty ( $this->uid )) {
			$this->throwException ( 'uid is empty', 1101 );
		}
		$gameuid = $mappingHD->get ( $this->uid );
		if (empty ( $gameuid )) {
			$this->register();
		} else {
		}
	}

}