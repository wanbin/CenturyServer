<?php
// 抽奖更新选项
include_once 'BaseCommand.php';
include_once 'handler/LotteryHandler.php';
class LotteryUpdateSetting extends BaseCommand {
	protected function executeEx($params) {
		$content = $params ['content'];
		// 是否是需要审核的词汇
		$lottery = new LotteryHandler ( $this->uid );
		$lottery->updateSetting ( $content );
		return $this->reutrnDate ( COMMAND_ENPTY, array () );
	}

}