<?php
include_once FRAMEWORK.'database/DBHelper2.class.php';
class DBHelperFactory {
	public static function getDBHelper($config,$id = ''){
    	if(empty($config)){
    		throw new Exception('config empty');
    	}elseif(is_string($config)){
    		// 使用字符串配置，多个数据库只有一台服务器的情况
    		$dbhelper = new DBHelper2($config);
			$options = $dbhelper->getConnectionOptions();
			$db_index = '';
			if(!empty($options['dbnum'])){
				$db_num = intval($options['dbnum']);
				if($db_num > 1){
					if(empty($id)){
			    		$id = 0;
			    	}
					$db_index = '_' . intval(($id / 100) % $db_num);
				}
			}
			if(!empty($options['prefix'])){
				$db_name = $options['prefix'] . $db_index;
				$dbhelper->changeDB($db_name);
			}
			return $dbhelper;
    	}elseif(is_array($config) && isset($config['server-map'])){
    	 	// 使用数组配置，分库后的数据库在多台服务器上
    	 	$db_num = intval($config['dbnum']);
    	 	$db_index = 0;
			if($db_num > 1){
				if(empty($id)){
					$id = 0;
				}
	    		$db_index = intval(($id / 100) % $db_num);
			}
			if($db_num != count($config['server-map'])){
				throw new Exception('dbnum error');
			}
			if(!empty($config['server-map'][$db_index])){
				return new DBHelper2($config['server-map'][$db_index]);
			}else{
				throw new Exception('db map not set: key ' . $db_index);
			}
    	 }else{
    	 	throw new Exception('db config error: it must be an array or a string');
    	 }
	}
}

?>