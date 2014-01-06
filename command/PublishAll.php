<?php
// 查询需要审核的词汇
include_once 'BaseCommand.php';
include_once 'handler/PublishHandler.php';
class PublishAll extends BaseCommand {
	protected function executeEx($params) {
		$content = $params ['content'];
		$page = $params ['page'];
		//是否是需要审核的词汇
		$shenhe = $params ['shenhe'];
		$publish = new PublishHandler ( $this->uid );
		if ($shenhe == 1) {
			$ret = $publish->getPageShenHe ( $page );
		} else {
			$ret = $publish->getPage ( $page );
		}
		return $this->reutrnDate ( COMMAND_SUCCESS, $ret );
	}

}