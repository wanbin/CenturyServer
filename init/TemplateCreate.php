<?php
//要创建的array创建完成之后别忘记删除无用的
$createarr = array ('Mapping','Runtime','Building' );
$createarr = array ('');
include_once 'CreateClass.php';
include_once '../Entry.php';

foreach ( $createarray as $key => $value ) {
	if (! in_array ( $value ['modelname'], $createarr )) {
		continue;
	}
	// 生成sql语句
	$temSql = '-- ' . $value ['description'] . ' --' . "\r\n" . 'CREATE TABLE `' . $value ['tablename'] . '`(
	replase PRIMARY KEY  (primarykey))ENGINE=InnoDB DEFAULT CHARSET=utf8;';
	$string ='';
	$keystring = '';
	$jsonstring = '';
	$primaryKeyCount = 0;
	$secPrimaryKey = '';
	//以下两个参数在update中用
	//搜索Where生成(gameuid = $this->gameuid,count=>count)
	$strWhere = '';
	// 定位参数生成($gameud,$count)
	$strUnion = '';
	//不包括Gameuid的用户名称
	$strWhereWithoutGameuid = '';
	// 定位array('gameuid','count')
	$strJson = '';
	$strAll = '';
	$ParamsWithoutGameuid = '';
	foreach ( $value ['tablefiled'] as $tableKey => $tableValue ) {
		if (in_array ( $tableValue ['type'], array ('varchar', 'int' ) )) {
			$string .= '`' . $tableKey . '` ' . $tableValue ['type'] . '(' . $tableValue ['lengh'] . ') default "' . $tableValue ['default'] . '" '."COMMENT '". $tableValue ['comment'] . "', \r\n" ;
		}
		if ($tableValue ['isPrimaryKey'] == 1) {
			$keystring .= '`' . $tableKey . '`,';
			if (in_array($tableKey,array('gameuid'))) {
				$strWhere .= ",'$tableKey' => $"."this->$tableKey";
			} else {
				$strWhere .= ",'$tableKey' => $$tableKey";
			}
			if (! in_array ( $tableKey, array ('gameuid' ) )) {
				$ParamsWithoutGameuid .= ",$$tableKey";
			}
			$strUnion.=",$$tableKey";
			$primaryKeyCount ++;
			if ($primaryKeyCount == 2) {
				$secPrimaryKey = $tableKey;
			}
		}
		if ($tableValue ['isjson'] == 1) {
			$strJson .= ",'" . $tableKey . "'";
		}
	}
	if ($primaryKeyCount == 0) {
		$string = rtrim($string);
		$string = rtrim($string,',');
		$temSql = str_replace ( 'PRIMARY KEY  (primarykey)', '', $temSql );
	}
	$temSql = str_replace ( 'replase', $string, $temSql );
	
	$temSql = str_replace ( 'primarykey', rtrim ( $keystring, ',' ), $temSql );
	
	
	
	// 生成commodel
	$tempModel = getSource ( 'TemplateModel','./tmp/' );
	$tempModel = str_replace ( 'TemplateModel', ucfirst ( $value ['modelname'] ) . 'Model', $tempModel );
	$tempModel = str_replace ( 'TemplatenContent', $value ['description'], $tempModel );
	$tempModel = str_replace ( 'templatetablename', $value ['tablename'], $tempModel );
	$tempModel = str_replace ( 'templatefields', implode ( ',', array_keys ( $value ['tablefiled'] ) ), $tempModel );
	$tempModel = str_replace ( '{strWhere} ',ltrim( $strWhere,','), $tempModel );
	$tempModel = str_replace ( '{strUnion}',ltrim( $strUnion,','), $tempModel );
	$tempModel = str_replace ( '{strJson}',ltrim( $strJson,','), $tempModel );
	
	$tempModel = str_replace ( '{Author}', $author, $tempModel );
	$tempModel = str_replace ( '{Date}', $date, $tempModel );

	// 生成cache
	$memcacheKey = 'MEMCACHE_KEY_' . strtoupper ( $value ['modelname'] );
	$tempCache = getSource ( 'TemplateCache' ,'./tmp/');
	$tempCache = str_replace ( 'TemplateModel', ucfirst ( $value ['modelname'] ) . 'Model', $tempCache );
	$tempCache = str_replace ( 'TemplateCache', ucfirst ( $value ['modelname'] ) . 'Cache', $tempCache );
	$tempCache = str_replace ( 'TemplatenContent', $value ['description'], $tempCache );
	$tempCache = str_replace ( '{paramsWithOutGameuid}', ltrim($ParamsWithoutGameuid,','), $tempCache );
	$tempCache = str_replace ( '{paramsWithOutGameuidSeparate}',$ParamsWithoutGameuid, $tempCache );
	$tempCache = str_replace ( '{strUnion}', $strUnion, $tempCache );
	$tempCache = str_replace ( 'MEMCACHE_KEY_TEMPLATECACHEKEY', $memcacheKey, $tempCache );
	
	$tempCache = str_replace ( '{Author}', $author, $tempCache );
	$tempCache = str_replace ( '{Date}', $date, $tempCache );
		
		// 生成缓存键
	$tempSystem = getSource ( 'SystemConstants' );
	if (strpos ( $tempSystem, $memcacheKey ) == false) {
		$temstr = '//' . $value ['description'] . ' ' . "\r\n";
		if ($value ['singleData'] == true) {
			$temstr .= 'define("' . $memcacheKey . '", "centurywar_' . $value ['modelname'] . '_%d");' . "\r\n";
		} else {
			$temstr .= 'define("' . $memcacheKey . '", "centurywar_' . $value ['modelname'] . '_%d_%d");' . "\r\n";
		}
		$temstr .= 'define("' . $memcacheKey . '_LIST", "centurywar_' . $value ['modelname'] . '_list_%d");' . "\r\n" . "\r\n";
		writeAPPEND ( 'SystemConstants', $temstr );
	}
	
	
		// 只能处理 有两个的情况
	if (! empty ( $secPrimaryKey )) {
		$tempModel = str_replace ( 'templateid', $secPrimaryKey, $tempModel );
		$tempCache = str_replace ( 'templateid', $secPrimaryKey, $tempCache );
		$tempModel = str_replace ( 'startGet', '', $tempModel );
		$tempCache = str_replace ( 'endGet', '', $tempCache );
	}
	else {
		//todu把没有用到的方法删除
// 		$tempModel = preg_replace ( '~startGet\.*endGet', 'empty', $tempModel );
// 		$tempCache = preg_replace ( '~startGet\.*endGet', 'empty', $tempCache );
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
	$filePath = $path . $fileName . '.php';
	if (! file_exists ( $filePath )) {
		return '';
	}
	return file_get_contents ( $filePath );
}
function writeOutPut($fileName, $content, $path = PATH_DATAOBJ, $type = 'php') {
	file_put_contents ( $path . $fileName . '.' . $type, $content );
}

function writeAPPEND($fileName, $content, $path = PATH_DATAOBJ) {
	$filePath = $path . $fileName . '.php';
	if (! file_exists ( $filePath )) {
		$content = "<?php \r\n" . $content;
	}
	file_put_contents ( $filePath, $content, FILE_APPEND );
}
