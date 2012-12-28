<?php
include_once 'BaseCommand.php';
include_once PATH_CACHE . 'MappingCache.php';
class Mapping extends BaseCommand {
	protected function executeEx($params) {
		$mappingMC = new MappingCache($this->gameuid);
		$mappingMC->setServer($this->server);
		$ret = $mappingMC->getOneByUid('wb6465');
		print_r($ret);
	}
}