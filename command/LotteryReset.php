<?php
// 抽奖更新选项
include_once 'BaseCommand.php';
include_once 'handler/LotteryHandler.php';
class LotteryReset extends BaseCommand {
	protected function executeEx($params) {
		// 是否是需要审核的词汇
		$lottery = new LotteryHandler ( $this->uid );
		$lottery->LotteryReset();
		$ret=$lottery->getSetting();
		return $this->returnDate ( COMMAND_ENPTY,$ret['content']);
	}

}