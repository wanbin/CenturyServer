<?php
// 抽奖更新选项
include_once 'BaseCommand.php';
include_once 'handler/LotteryHandler.php';
class LotteryGetSetting extends BaseCommand {
	protected function executeEx($params) {
		$id = $params['gameid'];
		// 是否是需要审核的词汇
		$lottery = new LotteryHandler ( $this->uid );
		$ret=$lottery->getSetting();
		return $this->reutrnDate ( COMMAND_ENPTY, $ret['content']);
	}

}