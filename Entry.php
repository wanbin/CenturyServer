<?php
include_once 'define.php';
date_default_timezone_set ( "Asia/Chongqing" );
error_reporting ( E_ALL ^ E_NOTICE );
// 游戏当前版本
class Entry {
	// 请求分发
	public static function callCommand($command, $param = array(), $sign_arr = array()) {
		static $commandInstance = array ();
		if (! empty ( $commandInstance [$command] )) {
			return $commandInstance [$command]->execute ( $command, $param, $sign_arr );
		}
		// 创建命令对象
		if (file_exists ( PATH_COMMAND . $command . '.php' )) {
			include_once PATH_COMMAND . $command . '.php';
			$cmd = new $command ();
			$commandInstance [$command] = $cmd;
			return $cmd->execute ( $command, $param, $sign_arr );
		} else {
			GameException::throwException ( 'command:' . $command . ' not exist', 3, 0, 0 );
		}
	}
}

$command = $_REQUEST ['cmd'];
$data = json_decode ( str_replace ( "\\", "", $_REQUEST ['data'] ), true );
$sign = json_decode ( str_replace ( "\\", "", $_REQUEST ['sign'] ), true );
Entry::callCommand ( $command, $data, $sign );
?>