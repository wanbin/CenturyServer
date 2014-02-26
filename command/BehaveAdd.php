<?php
// 查询需要审核的词汇
include_once 'BaseCommand.php';
include_once 'handler/BehaveHandler.php';
class BehaveAdd extends BaseCommand {
	protected function executeEx($params) {
		$data = $params ['data'];
		$behave = $params ['behave'];
		$sec = $params ['sec'];
		$behavea = new BehaveHandler ( $this->uid );
		$ret = $behavea->newBehave ( $behave,$data,$sec );
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}

}