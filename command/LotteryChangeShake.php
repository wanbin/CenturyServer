<?php
// 抽奖更新是否摇动增加概率
include_once 'BaseCommand.php';
include_once 'handler/LotteryHandler.php';
class LotteryChangeShake extends BaseCommand {
	protected function executeEx($params) {
		$isshake = $params['isshake']==true?1:0;
		// 是否是需要审核的词汇
		$lottery = new LotteryHandler ( $this->uid );
		$ret=$lottery->updateShake($isshake);
		return $this->returnDate ( COMMAND_ENPTY, array('isshake'=>$isshake));
	}
}