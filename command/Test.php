<?php
include_once 'BaseCommand.php';
include_once PATH_CACHE . 'TestContentCache.php';
class Test extends BaseCommand {
	protected function executeEx($params) {
		$skillMC = new TestContentCache($this->gameuid);
		$skillMC->addarr(array(array('gameuid'=>$this->gameuid+rand(1,1000),'count'=>rand(1,9999999))));
	}
	function randSkillId() {
		return rand ( 1001, 1010 );
	}
}