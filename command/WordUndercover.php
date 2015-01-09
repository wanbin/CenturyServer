<?php
// 谁是卧底词汇
include_once 'BaseCommand.php';
include_once 'handler/WordsHandler.php';
class WordUndercover extends BaseCommand {
	protected function executeEx($params) {
		global $word;
		$word=new WordsHandler($this->uid);
		$result=$word->getRandomOne();
		$ret ['word'] = $result['content'];
		$ret ['wordtype']=$result['type'];
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}
}