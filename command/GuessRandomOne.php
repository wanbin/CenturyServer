<?php
// 随机返回一条词汇
include_once 'BaseCommand.php';
class GuessRandomOne extends BaseCommand {
	protected function executeEx($params) {
		$type=$params['type'];
		// 是否是需要审核的词汇
		$ret=array();
		$return=30;
		include_once PATH_HANDLER . 'GuessHandler.php';
		$words = new GuessHandler ( $uid );
		$list=$words->getTypeList();
		$typearr=array();
		foreach ($list as $valuelist){
			$typearr [$valuelist [value]] = $valuelist ['content'];
		}
		for($return = 30; $return > 0; $return --) {
			$tem = $words->getRandomOne ($type);
			$ret [] = array (
					'key' => $typearr [$tem ['type']],
					'content' => $tem ['content'] 
			);
		}
		return $this->returnDate ( COMMAND_ENPTY, $ret );
	}
}