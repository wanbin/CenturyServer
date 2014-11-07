<?php
// 谁是卧底词汇
include_once 'BaseCommand.php';
include_once 'handler/WordsHandler.php';
class WordUndercover extends BaseCommand {
	protected function executeEx($params) {
		global $word;
		$ret ['word'] = $word [array_rand ( $word )];
		return $this->reutrnDate ( COMMAND_ENPTY, $ret );
	}
}