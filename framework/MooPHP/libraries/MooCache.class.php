<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: MooCache.class.php 387 2008-09-09 07:22:03Z kimi $
*/

!defined('IN_MOOPHP') && exit('Access Denied');

class MooCache {

	function arrayEval($array, $level = 0) {
		$space = '';
		for($i = 0; $i <= $level; $i++) {
			$space .= "\t";
		}
		$evaluate = "Array\n$space(\n";
		$comma = $space;
		if(is_array($array)) {
			foreach($array as $key => $val) {
				$key = is_string($key) ? '\''.addcslashes($key, '\'\\').'\'' : $key;
				$val = !is_array($val) && (!preg_match("/^\-?[1-9]\d*$/", $val) || strlen($val) > 12) ? '\''.addcslashes($val, '\'\\').'\'' : $val;
				if(is_array($val)) {
					$evaluate .= "$comma$key => ".$this->arrayEval($val, $level + 1);
				} else {
					$evaluate .= "$comma$key => $val";
				}
				$comma = ",\n$space";
			}
		}
		$evaluate .= "\n$space)";
		return $evaluate;
	}

	function getBlock($param) {

		$cachekey = md5($param);
		$param = $this->parseParameter($param);
		$cachekey = $param['name'].'_'.$cachekey;

		//note 判断是否需要缓存
		if(MOOPHP_ALLOW_BLOCK && $param['cachetime']) {
			$cacheArray = $this->getBlockCache($cachekey);
		} else {
			$cacheArray = array();
		}

		//note 判断是否需要应用缓存数据
		if(empty($cacheArray['mtime']) || $GLOBALS['timestamp'] - $cacheArray['mtime'] >= $param['cachetime']) {
			switch($param['type']) {
				case 'list':
					$cacheArray['values'] = $toding;
					break;
				case 'query':
					$cacheArray['values'] = $GLOBALS['_MooClass']['MooMySQL']->getAll($param['sql']);
					break;
				default:
					$cacheArray['values'] = NULL;
			}

			//note 判断是否需要更新缓存
			if(MOOPHP_ALLOW_BLOCK && $param['cachetime']) {
				//$cacheArray['multipage'] = $cacheArray['multi'];//缓存分页 debug
				$this->setBlockCache($cachekey, $cacheArray);
			}
		}

		//note 将Block的缓存数据存入全局变量中
		$GLOBALS['_MooBlock'][$param['name']] = $cacheArray['values'];
		//$GLOBALS['_MooBlock'][$param['name'].'_multipage'] = $cacheArray['multi'];//debug

	}

	function getBlockCache($cachekey) {

		$caches = array('mtime'=>0);
		$cachefile = MOOPHP_DATA_DIR.'/block/'.$cachekey.'.data';

		if(@file_exists($cachefile)) {
			if(@$fp = fopen($cachefile, 'r')) {
				@$data = fread($fp,filesize($cachefile));
				fclose($fp);
			}
			@$cacheArray = unserialize($data);
			if(isset($cacheArray['multipage'])) {
				$caches['multi'] = $cacheArray['multipage'];
				unset( $cacheArray['multipage']);
			} else {
				$caches['multi'] = '';
			}
			$caches['values'] = $cacheArray['values'];
			@$caches['mtime'] = filemtime($cachefile);
		}
		return $caches;
	}

	function parseParameter($param) {
		$paramarr = array();
		$parr = explode('/', $param);
		if(empty($parr)) return $paramarr;

		foreach($parr as $value){
			$valuearr = explode('=', $value, 2);
			$paramarr[$valuearr[0]] = $valuearr[1];
		}
		return $paramarr;
	}

	function setBlockCache($cacheKey, $cacheArray) {

		$blockContent = serialize($cacheArray);
		$blockDir = MOOPHP_DATA_DIR.'/block/';
		$blockFile = MOOPHP_DATA_DIR.'/block/'.$cacheKey.'.data';

		MooMakeDir($blockDir);
		MooWriteFile($blockFile, $blockContent);

	}

	function setCache($cacheFile) {

		if(!MOOPHP_ALLOW_CACHE) {
			return FALSE;
		}

		$cacheContent = '';

		foreach($GLOBALS['_MooCacheConfig'][$cacheFile] as $cacheKey) {
			$cacheFuncName = 'MooGetCache_'.$cacheKey;
			$cacheContent .= "\$_MooCache['$cacheKey'] = ".$this->arrayEval($cacheFuncName()).";\n\n";
		}

		$this->writeCache($cacheFile, $cacheContent);
	}

	function setCacheByKey($cacheKey) {

		if(!MOOPHP_ALLOW_CACHE) {
			return FALSE;
		}

		foreach($GLOBALS['_MooCacheConfig'] as $cacheFile => $cacheKeyArray) {
			foreach($cacheKeyArray as $key) {
				if($cacheKey == $key) {
					$this->setCache($cacheFile);
				}
			}
		}

	}

	function writeCache($cacheFile, $cacheContent) {

		$cacheContent = "<?php\n//MooPHP Cache File, Do Not Modify Me!".
				"\n//Created: ".date("Y-m-d H:i:s").
				"\n$cacheContent?>";


		$cacheDir = MOOPHP_DATA_DIR.'/cache/';
		$cacheFile = MOOPHP_DATA_DIR.'/cache/cache_'.$cacheFile.'.php';

		MooMakeDir($cacheDir);
		MooWriteFile($cacheFile, $cacheContent);

	}
}
?>