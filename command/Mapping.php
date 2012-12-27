<?php
include_once 'BaseCommand.php';
include_once PATH_CACHE . 'MappingCache.php';
class Mapping extends BaseCommand {
	protected function executeEx($params) {
		$mappingMC = new MappingCache($this->gameuid);
		$mappingMC->addone($this->uid);
	}
}