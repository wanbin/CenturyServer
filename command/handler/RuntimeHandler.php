<?php
require_once PATH_CACHE . 'RuntimeCache.php';
class RuntimeHandler extends RuntimeCache{
	public function get() {
		return $this->getOneFromCache ();
	}
	public function addUpgrade($id, $level, $time, $costtime) {
		$res = $this->getOneFromCache ();
		$res ['upgrade'] [$id] = array (
				'level' => $level,
				'time' => $time,
				'costtime' => $costtime
		);
		return $this->update ( $res );
	}
	public function removeUpgrade($id) {
		$res = $this->getOneFromCache ();
		unset ( $res ['upgrade'] [$id] );
		return $this->update ( $res );
	}
}