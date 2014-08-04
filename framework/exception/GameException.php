<?php
/**
 * @author wanbin
 *
 */
class GameException extends Exception {
	protected $gameuid;
	protected $uid;
	protected $extra = null;
	public function __construct($message, $code, $uid = null, $gameuid = null, $extra_info = null) {
		try {
			parent::__construct ( $message, $code );
		} catch ( Exception $e ) {
			file_put_contents ( PATH_ROOT . '../logs/ExceptionClass.log', print_r ( $e, true ), FILE_APPEND );
		}
		$this->uid = $uid;
		$this->gameuid = $gameuid;
		$this->extra = $extra_info;
	}
	public function getUid() {
		return $this->uid;
	}
	public function getGameuid() {
		return $this->gameuid;
	}
	public function __toString() {
		$msg = sprintf ( "Code:%d\tMsg:%s. uid=%s,gameuid=%s.", $this->code, $this->message, $this->uid, $this->gameuid );
		if ($this->extra !== null) {
			if (is_array ( $this->extra )) {
				$msg .= "\nExtra-info:" . print_r ( $this->extra, true );
			} else {
				$msg .= "\nExtra-info:" . $this->extra;
			}
		}
		if (defined ( 'DEBUG' ) && DEBUG) {
			$msg .= "\nTrace:" . $this->getTraceAsString ();
		}
		return $msg;
	}
	/**
	 * 抛出异常或者返回amf错误并结束脚本。如果exit参数设置为false，则抛出异常，
	 * 如果exit设置true，则直接输出amf的错误消息，并结束php的执行
	 *
	 * @param string $message
	 *        	错误消息
	 * @param int $code
	 *        	错误代码
	 * @param int $uid
	 *        	相关的用户uid
	 * @param int $gameuid
	 *        	相关的用户gameuid
	 * @param bool $exit
	 *        	是否输出消息后终止脚本
	 * @return void
	 */
	public static function throwException($message, $code, $uid, $gameuid, $exit = true, $commandName = '') {
		// 如果是从amfphp的gateway过来，则直接返回错误的结果并退出脚本
		if (defined ( 'AMFPHP_BASE' ) && $exit && ! DEBUG) {
			self::writeErrorLog ( $message, $code, $uid, $gameuid, $commandName );
			if (empty ( $code )) {
				$code = 'UNKNOWN_ERROR';
			}
			throw new GameException ( $message, $code, $uid, $gameuid );
			$result = array (
					'__code' => $code,
					'__msg' => $message
			);
		} else {
			// 如果是从其他流程过来的，则抛出异常
			self::writeErrorLog ( $message, $code, $uid, $gameuid, $commandName );
			throw new GameException ( $message, $code, $uid, $gameuid );
			exit ();
		}
	}
	/**
	 * 写错误消息，成功返回true，失败返回false
	 *
	 * @param $message 错误消息
	 * @param $code 错误代码
	 * @param $uid 相关的用户uid
	 * @param $gameuid 相关的用户gameuid
	 * @return bool
	 */
	public static function writeErrorLog($message, $code, $uid, $gameuid, $commandName) {
		$filePath = $GLOBALS ['config'] ['log_path'] . 'error_logs/';
		if (! file_exists ( $GLOBALS ['config'] ['log_path'] . 'error_logs' )) {
			mkdir ( $GLOBALS ['config'] ['log_path'] . 'error_logs', 0777, true );
		}
		$msg = sprintf ( "[%s][err]", date ( 'Y-m-d H:i:s' ) );
		if ($uid) {
			$msg .= " uid=$uid ";
		}
		if ($gameuid) {
			$msg .= " gameuid=$gameuid";
		}
		$msg .= "commandName:" . $commandName . ", Code:$code,Msg:$message\n";
		$log_dir = $filePath . date ( 'Ym' );
		// 把log按月分文件夹
		$log_file = sprintf ( '%s/goe_error_%s.log', $log_dir, date ( 'Ymd' ) );
		if (! file_exists ( $log_dir )) {
			mkdir ( $log_dir, 0777, true );
		}
		return error_log ( $msg, 3, $log_file );
	}
	public static function writeLog($message, $code, $uid, $gameuid, $commandName) {
		$filePath = $GLOBALS ['config'] ['log_path'] . 'custom_logs/';
		if (! file_exists ( $GLOBALS ['config'] ['log_path'] . 'custom_logs' )) {
			mkdir ( $GLOBALS ['config'] ['log_path'] . 'custom_logs', 0777, true );
		}
		$msg = sprintf ( "[%s][err]", date ( 'Y-m-d H:i:s' ) );
		if ($uid) {
			$msg .= " uid=$uid ";
		}
		if ($gameuid) {
			$msg .= " gameuid=$gameuid";
		}
		$msg .= "commandName:" . $commandName . ", Code:$code,Msg:$message\n";
		$log_dir = $filePath . date ( 'Ym' );
		// 把log按月分文件夹
		$log_file = sprintf ( '%s/goe_error_%s.log', $log_dir, date ( 'Ymd' ) );
		if (! file_exists ( $log_dir )) {
			mkdir ( $log_dir, 0777, true );
		}
		file_put_contents ( $log_file, $msg, FILE_APPEND );
		return true;
	}
}
