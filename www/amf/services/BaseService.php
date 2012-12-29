<?php
include_once dirname ( __FILE__ ) . '/../../../Entry.php';
include_once FRAMEWORK . 'utility/TimeUtil.class.php';
class BaseService {
	public function __construct() {
	
	}
	public function dispatch($request) {
		$sign_arr = array ();
		$sign_arr ['uid'] = $request ['uid'];
		$sign_arr ['sns_id'] = $request ['pid'];
		$sign_arr ['gameuid'] = $request ['guid'];
		$sign_arr ['server'] = $request ['server'];
		$sign_arr ['version'] = $request ['v'];
		
		if (empty ( $request ['pid'] )) {
			return false;
		}
		$res = array ();
		$timer = new TimeUtil ();
		$timer->start ();
		foreach ( $request ['data'] as $data ) {
			$result = array ();
			try {
				$data ['params'] = array_merge ( $data ['params'], array ('sns_id' => $sign_arr ['sns_id'] ) );
				$result = Entry::callCommand ( $data ['cmd'], $data ['params'], $sign_arr );
			} catch ( Exception $e ) {
				$result ['status'] = 0;
				$result ['cmd'] = $data ['cmd'];
				$result ['__code'] = $e->getCode ();
				$result ['__message'] = $e->getMessage ();
				if (DEBUG) {
					$result ['__line'] = $e->getFile () . '[' . $e->getLine () . ']';
				}
				$res ['result'] [] = $result;
				break;
			}
			$res ['result'] [] = $result;
		}
		$timer->stop ();
		$res ['t'] = time ();
		if (in_array ( $sign_arr ['version'], $GLOBALS ['config'] ['version'] )) {
			$res ['v'] = $sign_arr ['version'];
		} else {
			$res ['v'] = $GLOBALS ['config'] ['version_default'];
		}
		$res ['performance'] = $timer->spent () . sprintf ( ' memory usage: %01.2f MB', memory_get_usage () / 1024 / 1024 );
		return $res;
	}
}

?>