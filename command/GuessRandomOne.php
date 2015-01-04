<?php
// 随机返回一条词汇
include_once 'BaseCommand.php';
class GuessRandomOne extends BaseCommand {
	protected function executeEx($params) {
		// 是否是需要审核的词汇
		$ret=array();
		$return=30;
		for($return=30;$return>0;$return--){
			$ret[]=$this->getRandomOne();
		}
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}
	private function getRandomOne(){
		global $guess;
		/*
		$total = 0;
		foreach ( $guess as $key => $value ) {
			$total += count ( $value );
		}
		$random = rand ( 1, $total );
		foreach ( $guess as $key => $value ) {
			if (count ( $value ) < $random) {
				return array (
						'key' => $key,
						'content' => $value [$random] 
				);
			}
			$random -= count ( $value );
		}*/
		$temarray = array_rand ( $guess );
		$content = $guess [$temarray] [array_rand ( $guess [$temarray] )];
		return array (
				'key' => $temarray,
				'content' => $content 
		);
	}	
}