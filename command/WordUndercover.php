<?php
// 谁是卧底词汇
include_once 'BaseCommand.php';
include_once 'handler/WordsHandler.php';
class WordUndercover extends BaseCommand {
	protected function executeEx($params) {
		$type=isset($params['type'])?$params['type']:0;
		global $word;
		$word=new WordsHandler($this->uid);
		//避免删除某个词汇到，用户没有返回正常的词汇
		$result = array ();
		while ( empty ( $result ) ) {
			$result = $word->getUniqueOne ( $type );
		}
		$ret ['word'] = $result['content'];
		$ret ['wordtype']=$result['type'];
		return $this->returnDate ( COMMAND_ENPTY, $ret );
	}
}