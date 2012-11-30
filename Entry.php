<?php
// 游戏主程序调用入口
define( 'PATH_ROOT', dirname(__FILE__).'/' );			// 根目录
define( 'PATH_COMMAND', PATH_ROOT.'command/');			// 命令(逻辑)目录
define( 'PATH_DATAOBJ', PATH_ROOT.'dataobj/');			// 数据对象目录
define( 'PATH_CACHE', PATH_ROOT.'dataobj/cache/');      // mc层
define( 'FRAMEWORK', PATH_ROOT . 'framework/');         //主框架目录

include PATH_ROOT . 'config.host.php';			// 全局配置文件
include PATH_ROOT . 'config.inc.php';

//加载framework中db操作类与cache操作类
require_once FRAMEWORK . '/cache/Cache.class.php';
require_once FRAMEWORK . '/database/DBHelper2.class.php';
//加载redies
require_once FRAMEWORK . '/redis/Rediska.php';
include_once PATH_DATAOBJ . 'GameException.php'; // 游戏内部异常处理

//游戏当前版本
define('DEBUG',true);
class Entry
{
	// 请求分发
	public static function callCommand( $command, $param=array(), $sign_arr)
	{
		
		static $commandInstance = array();
		if(!empty($commandInstance[$command]))
		{
			return $commandInstance[$command]->execute( $command, $param, $sign_arr);
		}
		// 创建命令对象
		if(file_exists(PATH_COMMAND.$command.'.php'))
		{
			include_once PATH_COMMAND.$command.'.php';
			$cmd = new $command();
			$commandInstance[$command] = $cmd;
			return $cmd->execute( $command, $param, $sign_arr);
		}
		else
		{
			GameException::throwException('command:'.$command.' not exist',3,0,0);
		}
	}
}

?>