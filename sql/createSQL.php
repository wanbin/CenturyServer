<?php
set_time_limit(60 * 2);
define('PATH_SQL', dirname(__FILE__).'/' );

//引入配置文件
$configPath = PATH_SQL . '../../conf/config.host.php';
$configPath = PATH_SQL . '../config.inc.php';
if(!file_exists($configPath)){
    exit($configPath . ' is not exits.');
}
include_once PATH_SQL.'../Entry.php';

include_once $configPath;
$dbConfig = $config['dbconfig'];
//清空output目录
$fso  = opendir(PATH_SQL . './output/');
while($flist=readdir($fso)){
    if($flist != '.' && $flist != '..'){
         $output[] = $flist;
    }
}


if(!empty($output)){
    foreach ($output as $value){
        //unlink(PATH_SQL . './output/' . $value);   
    }
}
//得到source目录的文件列表
$fso  = opendir(PATH_SQL . './source/');
while($flist=readdir($fso)){
    if($flist != '.' && $flist != '..' && $flist != '.svn'){
         $list[] = str_replace('.sql', '', $flist);
    }
}
closedir($fso);
$tables = $list;

$dbNameArray = array();
foreach ($tables as $value){
    if(file_exists(PATH_SQL . 'source/' . $value . '.sql')){
        if(!array_key_exists($value, $dbConfig)){
            $node = 'default';
        }else{
            $node = $value;
        }
        $host = $dbConfig[$node]['host'];
        $ip = $dbConfig[$host]['host'];
        $fileName = str_replace('.', '_', $ip) . '.sql';
        $dbName = $dbConfig[$node]['db_name'];
        $sql = getSource($value);
        $dbNum = $dbConfig[$node]['db_num'];
        $tableNum = $dbConfig[$node]['table_num'];
        for($i = 0 ; $i < $dbNum ; $i ++){
            $sqlContent = '';
            $cleanDatabase = '';
            $changeDatabase = '';
            $dbNameSub = $dbName . ($dbNum == 1 ? '' : '_'.$i);
            if(array_key_exists($dbNameSub, $dbNameArray)){
                $sqlContent .= 'use ' . $dbNameSub . ';';
            }else{
                $sqlContent .= str_replace('databaseName', $dbNameSub, getCreateDBCode());
                $dbNameArray[$dbNameSub] = 1;
            }
  
            for($m = 0 ; $m < $tableNum ; $m ++){
				$tableName = $value . ($tableNum == 1 ? '' : '_' . $m);
				//水平分服
				if (in_array ( $value, $config ['dispatchTable'] )) {
					for($n = 0; $n < $config ['dispatchCount']; $n ++) {
						
						$sqlContent .= str_replace ( $value, $tableName . '__' . $n, $sql );
						$cleanDatabase .= 'use ' . $dbNameSub . '; TRUNCATE  `' . $tableName . '__' . $n . '`;' . "\r\n";
					}
				} else {
					$sqlContent .= str_replace ( $value, $tableName, $sql );
					$cleanDatabase .='use ' . $dbNameSub . '; TRUNCATE  `' . $tableName . '`;' . "\r\n";
				}
               
            }
            writeOutPut($fileName, $sqlContent);
            writeOutPut('clean.sql', $cleanDatabase);
        }
    }else{
        exit($value.'.sql is not exits.');
    }
}
$dropDatabase = "drop database ".$dbNameSub.";"."\r\n";
writeOutPut('drop.sql', $dropDatabase);
function getCreateDBCode(){
    $createDatabase = <<<AAA
\r\n-- 创建数据库 -- 
CREATE DATABASE `databaseName` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
use databaseName;\r\n
AAA;
    return $createDatabase;
}
function getSource($fileName){
    return file_get_contents(PATH_SQL . '/source/' . $fileName . '.sql');
}
function writeOutPut($fileName,$content){
    file_put_contents(PATH_SQL . '/output/' . $fileName, $content, FILE_APPEND);
}
echo 'all sucess!';

?>