<?php
// 抽奖更新选项
include_once 'BaseCommand.php';
include_once 'handler/LotteryHandler.php';
class LotteryDo extends BaseCommand {
	protected function executeEx($params) {
		$ismore = $params['morepeople'];
		// 是否是需要审核的词汇
		$lottery = new LotteryHandler ( $this->uid );
		$ret=$lottery->LotteryDo($ismore);
		return $this->returnDate ( COMMAND_ENPTY, $ret );
	}

}