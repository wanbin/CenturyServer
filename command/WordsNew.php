<?php
// 新建一条记录
include_once 'BaseCommand.php';
include_once 'handler/WordsHandler.php';
class WordsNew extends BaseCommand {
	protected function executeEx($params) {
		$content = $params ['data'] ;
		$username = $params ['username'];
		if (empty ( $content )) {
			$this->throwException ( 'content is empty', 1101 );
		}
		if (! empty ( $username )) {
			include_once PATH_HANDLER . 'AccountHandler.php';
			$account = new AccountHandler ( $this->uid );
			$account->changeUserName ( $username );
		}
		$words = new WordsHandler ( $this->uid );
		foreach ( $content as $key => $value ) {
			$words->newWords ( $value ['words1'], $value ['words2'], $value ['type'] );
		}
		return $this->reutrnDate ( COMMAND_SUCCESS );
	}
}