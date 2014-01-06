<?php
include_once 'define.php';
date_default_timezone_set ( "Asia/Chongqing" );
include_once PATH_ROOT . 'config.inc.php'; // 全局配置文件
include_once PATH_COMMAND . 'BaseCommand.php';

include_once 'config.code.php'; // 常量处理类
error_reporting ( E_ALL ^ E_NOTICE );
// 游戏当前版本
define ( 'DEBUG', true );
define ( 'TEST', true );
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
file_put_contents ( "getFromClient.log", print_R ( $_REQUEST, true ), FILE_APPEND );
$command = $_REQUEST ['cmd'];
$data = json_decode ( str_replace ( "\\", "", $_REQUEST ['data'] ), true );
$sign = json_decode ( str_replace ( "\\", "", $_REQUEST ['sign'] ), true );
Entry::callCommand ( $command, $data, $sign );

?>