<?php
//要创建的array创建完成之后别忘记删除无用的
$createarr = array ('updateLog' );
$createarr = array ('');
$createarray=array(
		array(
				'modelname' => 'action',
				'singleData' => true,
				'tablename' => 'user_action',
				'tablefiled' => array ('gameuid' => array('type'=>'int','lengh'=>10,'default'=>0), 
										'id' => array('type'=>'int','lengh'=>10,'default'=>0),
										'actions'=>array('type'=>'varchar','lengh'=>500,'default'=>'""'),
										'updatetime'=>array('type'=>'int','lengh'=>10,'default'=>0)
						),
				'description' => '用户动作类',
				),
		array(
				'modelname' => 'activities',
				'singleData' => true,
				'tablename' => 'user_activities',
				'tablefiled' => array ('gameuid' => array('type'=>'int','lengh'=>10,'default'=>0),
						'id' => array('type'=>'int','lengh'=>10,'default'=>0),
						'activities'=>array('type'=>'varchar','lengh'=>500,'default'=>'""'),
						'updatetime'=>array('type'=>'int','lengh'=>10,'default'=>0)
				),
				'description' => '用户活动类',
				),
		array(
				'modelname' => 'Message',
				'singleData' => true,
				'tablename' => 'user_friend_message',
				'tablefiled' => array ('gameuid' => array('type'=>'int','lengh'=>10,'default'=>0),
										'fgameuid' => array('type'=>'int','lengh'=>10,'default'=>0),
										'type' => array('type'=>'int','lengh'=>10,'default'=>0),
										'createtime'=>array('type'=>'int','lengh'=>10,'default'=>0),
										'content'=>array('type'=>'varchar','lengh'=>500,'default'=>'[]')
				),
				'description' => '用户好友间互动信息类',
		),
		array(
				'modelname' => 'updateLog',
				'singleData' => true,
				'tablename' => 'user_update_log',
				'tablefiled' => array ('gameuid' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>1),
						'createtime'=>array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>1),
						'type' => array('type'=>'int','lengh'=>2,'default'=>0,'isPrimaryKey'=>1),
						'cost' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0),
						'remain' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0),
						'command'=>array('type'=>'varchar','lengh'=>50,'default'=>'[]','isPrimaryKey'=>0),
						'content'=>array('type'=>'varchar','lengh'=>100,'default'=>'[]','isPrimaryKey'=>0,'isjson'=>1)
				),
				'description' => '用户消费记录',
		),
		array(
				'modelname' => 'commandAnalysis',
				'singleData' => true,
				'tablename' => 'command_analysis',
				'tablefiled' => array ('gameuid' => array('type'=>'int','lengh'=>10,'default'=>0),
						'command'=>array('type'=>'varchar','lengh'=>100,'default'=>'[]'),
						'createtime'=>array('type'=>'int','lengh'=>10,'default'=>0),
						'type' => array('type'=>'varchar','lengh'=>15,'default'=>''),
						'count' => array('type'=>'int','lengh'=>10,'default'=>0),
				),
				'description' => '方法性能分析',
		),
);
include_once '../Entry.php';


foreach ( $createarray as $key => $value ) {
	if (! in_array ( $value ['modelname'], $createarr )) {
		continue;
	}
	// 生成commodel
	$tempModel = getSource ( 'TemplateModel' );
	$tempModel = str_replace ( 'TemplateModel', ucfirst ( $value['modelname'] ) . 'Model', $tempModel );
	$tempModel = str_replace ( 'TemplatenContent', $value['description'], $tempModel );
	$tempModel = str_replace ( 'templatetablename', $value['tablename'], $tempModel );
	$tempModel = str_replace ( 'templatefields', implode ( ',', array_keys ( $value['tablefiled'] ) ), $tempModel );
	
	
	
	
	// 生成cache
	$memcacheKey =  'MEMCACHE_KEY_' . strtoupper ( $value ['modelname'] );
	$tempCache = getSource ( 'TemplateCache', PATH_CACHE );
	$tempCache = str_replace ( 'TemplateModel', ucfirst ( $value ['modelname'] ) . 'Model', $tempCache );
	$tempCache = str_replace ( 'TemplateCache', ucfirst ( $value ['modelname'] ) . 'Cache', $tempCache );
	$tempCache = str_replace ( 'TemplatenContent', $value ['description'], $tempCache );
	$tempCache = str_replace ( 'MEMCACHE_KEY_TEMPLATECACHEKEY',$memcacheKey, $tempCache );
	
		
		// 生成缓存键
	$tempSystem = getSource ( 'SystemConstants' );
	if (strpos ( $tempSystem, $memcacheKey ) == false) {
		$temstr = '//' . $value ['description'] . ' ' . "\r\n";
		$temstr .= 'define("' . $memcacheKey . '", "goe_' . $value ['modelname'] . '_%d");' . "\r\n" ;
		$temstr .= 'define("' . $memcacheKey . '_ALL", "goe_' . $value ['modelname'] . '_all_%d");' . "\r\n" . "\r\n";
		writeAPPEND ( 'SystemConstants', $temstr );
	}
	
	// 生成sql语句
	$temSql = '-- ' . $value ['description'] . ' --' . "\r\n" . 'CREATE TABLE `' . $value ['tablename'] . '`(
	replase PRIMARY KEY  (primarykey))ENGINE=InnoDB DEFAULT CHARSET=utf8;';
	$string ='';
	$keystring = '';
	$jsonstring = '';
	$primaryKeyCount = 0;
	$secPrimaryKey = '';
	foreach ( $value ['tablefiled'] as $tableKey => $tableValue ) {
		if (in_array ( $tableValue ['type'], array ('varchar', 'int' ) )) {
			$string .= '`' . $tableKey . '` ' . $tableValue ['type'] . '(' . $tableValue ['lengh'] . ') default "' . $tableValue ['default'] . '",'. "\r\n" ;
		}
		if ($tableValue ['isPrimaryKey'] == 1) {
			$keystring .= '`' . $tableKey . '`,';
			$primaryKeyCount ++;
			if ($primaryKeyCount == 2) {
				$secPrimaryKey = $tableKey;
			}
		}
		if ($tableValue ['isjson'] == 1) {
			$jsonstring .= "'" . $tableKey . "',";
		}
	}
	$temSql = str_replace ( 'replase', $string, $temSql );
	$temSql = str_replace ( 'primarykey', rtrim ( $keystring, ',' ), $temSql );
		
		// 只能处理 有两个的情况
	if (! empty ( $secPrimaryKey )) {
		$tempModel = str_replace ( 'templateid', $secPrimaryKey, $tempModel );
		$tempCache = str_replace ( 'templateid', $secPrimaryKey, $tempCache );
	}
	
	if (! empty ( $jsonstring )) {
		$tempModel = str_replace ( "'template_json'", rtrim ( $jsonstring, ',' ), $tempModel );
	}
	
	writeOutPut ( ucfirst ( $value['modelname'] ) . 'Model', $tempModel );
	writeOutPut ( ucfirst ( $value ['modelname'] ) . 'Cache', $tempCache ,PATH_CACHE);
	writeOutPut ( $value ['tablename'], $temSql, PATH_SQL.'/source/', 'sql' );

	echo 'create model ' . $value ['modelname'] . ' success!<br/>';
}

function getSource($fileName, $path = PATH_DATAOBJ) {
	return file_get_contents ( $path . $fileName . '.php' );
}
function writeOutPut($fileName, $content, $path = PATH_DATAOBJ, $type = 'php') {
	file_put_contents ( $path . $fileName . '.' . $type, $content );
}

function writeAPPEND($fileName, $content, $path = PATH_DATAOBJ) {
	file_put_contents ( $path . $fileName . '.php', $content, FILE_APPEND );
}
