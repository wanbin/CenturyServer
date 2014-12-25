<?php
// 随机返回一条词汇
include_once 'BaseCommand.php';
class ActRandomOne extends BaseCommand {
	protected function executeEx($params) {
		// 是否是需要审核的词汇
		global $des, $action;
		$ret = array (
				'content' => array (
						'des' => $des [array_rand ( $des )],
						'action' => $action [array_rand ( $action )] 
				) 
		);
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}
}