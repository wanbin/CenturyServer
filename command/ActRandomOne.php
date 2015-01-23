<?php
// 随机返回一条词汇
include_once 'BaseCommand.php';
class ActRandomOne extends BaseCommand {
	protected function executeEx($params) {
		// 是否是需要审核的词汇
		include_once PATH_HANDLER . 'ActionHandler.php';
		$words = new ActionHandler ( $uid );
		$des=$words->getRandomOne(1);
		$act=$words->getRandomOne(2);
		$ret = array (
				'content' => array (
						'des' => $des['content'],
						'action' => $act['content']
				) 
		);
		return $this->returnDate ( COMMAND_ENPTY, $ret );
	}
}