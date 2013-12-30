<?php
include_once 'BaseCommand.php';
include_once PATH_CACHE . 'TestContentCache.php';
class Test extends BaseCommand {
	protected function executeEx($params) {
		echo "dadfddda";
	}
	function randSkillId() {
		return rand ( 1001, 1010 );
	}
}