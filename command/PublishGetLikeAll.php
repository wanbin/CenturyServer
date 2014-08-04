<?php
// 从网上获取喜欢的真心话大冒险，只在第一次的时候取
include_once 'BaseCommand.php';
include_once 'handler/PublishHandler.php';
class PublishRandomOne extends BaseCommand {
	protected function executeEx($params) {
		$content = $params ['content'];
		$page = $params ['page'];
		// 是否是需要审核的词汇
		$shenhe = $params ['shenhe'];
		$publish = new PublishHandler ( $this->uid );
		$ret =array('content'=>$publish->getRandomOne ( 1 )) ;
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}
}