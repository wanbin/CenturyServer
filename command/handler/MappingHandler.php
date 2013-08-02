<?php
require_once PATH_CACHE . 'MappingCache.php';
class MappingHandler extends MappingCache{
	public function __construct($server = 1) {
		$this->server = $server;
		parent::__construct ();
	}
	public function get($uid) {
		return $this->getOneByUid ( $uid );
	}
	public function init($gameuid, $uid) {
		parent::init ( $gameuid, $uid );
	}
}