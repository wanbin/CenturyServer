<?php
// 抽奖更新选项
include_once 'BaseCommand.php';
include_once 'handler/LotteryHandler.php';
class LotteryDo extends BaseCommand {
	protected function executeEx($params) {
		$ismore = $params['ismore'];
		$isergodic = $params['isergodic'];
		$isshack = $params['isshack'];
		$content= json_decode($params['content']);
		
		// 是否是需要审核的词汇
		$lottery = new LotteryHandler ( $this->uid );
		$lottery->updateSetting($content, $isshack, $ismore, $isergodic);
		return $this->reutrnDate ( COMMAND_ENPTY, array() );
	}

}