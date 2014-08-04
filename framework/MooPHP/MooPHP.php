<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: MooPHP.php 401 2008-10-14 07:37:07Z kimi $
*/


//note ����MooPHP��ܻ���
define('IN_MOOPHP', TRUE);
//note MooPHP�ĺ��İ汾�����磺0.21 alpha
define('MOOPHP_VERSION', '0.95.399 beta');
//note ���ڱ����ʵ��ļ�·�������磺D:\web\MooPHP\
define('MOOPHP_ROOT', substr(__FILE__, 0, -10));
//note ���ڱ����ʵ��ļ�URL�����磺http://www.ccvita.com/MooPHP
define('MOOPHP_URL', strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], '/'))).'://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
//note REQUEST_URI
define('REQUEST_URI', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : (isset($_SERVER['argv']) ? $_SERVER['PHP_SELF'].'?'.$_SERVER['argv'][0] : $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING']));
define('MOOPHP_SELFURL', MOOPHP_URL.'/'.basename($_SERVER['PHP_SELF']));
define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

//note ��ֹ��ȫ�ֱ���ע��
if (isset($_REQUEST['GLOBALS']) OR isset($_FILES['GLOBALS'])) {
	exit('Request tainting attempted.');
}

//note MooPHP������
$_MooPHP = $_MooBlock = $_MooCacheConfig = $_MooCookie = $_MooClass = array();
//note ��ݿ���Ϣ��ʼ��
$dbHost = $dbName = $dbUser = $dbPasswd = $dbPconnect = '';
//note ��ʼ�����ñ���
$timestamp = time();
$mtime = explode(' ', microtime());
$_MooPHP['startTime'] = $mtime[1] + $mtime[0];

//note ����MooPHP�����ļ�
if(!defined('MOOPHP_USER_CONFIG')) {
	require_once MOOPHP_ROOT.'/MooConfig.php';
}

//note ����MooPHP���ó���
!defined('MOOPHP_ALLOW_BLOCK') && define('MOOPHP_ALLOW_BLOCK', TRUE);
!defined('MOOPHP_ALLOW_CACHE') && define('MOOPHP_ALLOW_CACHE', FALSE);
!defined('MOOPHP_ALLOW_MYSQL') && define('MOOPHP_ALLOW_MYSQL', FALSE);
// !defined('MOOPHP_DATA_DIR') && define('MOOPHP_DATA_DIR', MOOPHP_ROOT.'./../Moo-data');
// !defined('MOOPHP_TEMPLATE_DIR') && define('MOOPHP_TEMPLATE_DIR', '../Moo-templates');
!defined('MOOPHP_TEMPLATE_URL') && define('MOOPHP_TEMPLATE_URL', 'Moo-templates');
!defined('MOOPHP_ADMIN_DIR') && define('MOOPHP_ADMIN_DIR', 'Moo-admin');
!defined('MOOPHP_COOKIE_PRE') && define('MOOPHP_COOKIE_PRE', 'Moo');
!defined('MOOPHP_COOKIE_PATH') && define('MOOPHP_COOKIE_PATH', '/');
!defined('MOOPHP_COOKIE_DOMAIN') && define('MOOPHP_COOKIE_DOMAIN', '');
!defined('MOOPHP_AUTHKEY') && define('MOOPHP_AUTHKEY', 'kimi');
!defined('MOOPHP_DEBUG') && define('MOOPHP_DEBUG', FALSE);

//note ����MooPHPCache�����ļ�
if(MOOPHP_ALLOW_BLOCK) {
	MOOPHP_ALLOW_CACHE && require_once MOOPHP_ROOT.'/MooCacheConfig.php';
	$cache = MooAutoLoad('MooCache');
}

//note ���ϵͳ��Ҫʹ��MYSQL�򣬳�ʼ��
if(MOOPHP_ALLOW_MYSQL) {
	$db = MooAutoLoad('MooMySQL');
	$db->connect($dbHost, $dbUser, $dbPasswd, $dbName, $dbPconnect, $dbCharset);
}

//note ��GPC�������а�ȫ����
if (!MAGIC_QUOTES_GPC) {
	$_GET = MooAddslashes($_GET);
	$_POST = MooAddslashes($_POST);
	$_COOKIE = MooAddslashes($_COOKIE);
	$_REQUEST = MooAddslashes($_REQUEST);
}

//note ����php.ini�е�magic_quotes_gpc���ò����$_SERVER��$_FILES����Ӱ�죬MooPHP�Ӱ�ȫ�Ƕȳ�������$_SERVER��$_FILES�����ת�塣
$_SERVER = MooAddslashes($_SERVER);
$_FILES = MooAddslashes($_FILES);

$CookiePreLength = strlen(MOOPHP_COOKIE_PRE);
foreach($_COOKIE as $key => $val) {
	if(substr($key, 0, $CookiePreLength) == MOOPHP_COOKIE_PRE) {
		$_MooCookie[(substr($key, $CookiePreLength))] = MAGIC_QUOTES_GPC ? $val : MooAddslashes($val);
	}
}
unset($CookiePreLength);

!MOOPHP_AUTHKEY && exit('MOOPHP_AUTHKEY is not defined!');

/**
* ����RC4Ϊ�����㷨��ͨ����ܻ��߽����û���Ϣ
* @param $string - ���ܻ���ܵĴ�
* @param $operation - DECODE ���ܣ�ENCODE ����
* @param $key - ��Կ Ĭ��ΪMOOPHP_AUTHKEY����
* @return �����ַ�
*/
function MooAuthCode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	/**
	* $ckey_length �����Կ���� ȡֵ 0-32;
	* ���������Կ���������������κι��ɣ�������ԭ�ĺ���Կ��ȫ��ͬ�����ܽ��Ҳ��ÿ�β�ͬ�������ƽ��Ѷȡ�
	* ȡֵԽ�����ı䶯����Խ�����ı仯 = 16 �� $ckey_length �η�
	* ����ֵΪ 0 ʱ���򲻲��������Կ
	*/
	$ckey_length = 4;
	$key = md5($key ? $key : md5(MOOPHP_AUTHKEY.$_SERVER['HTTP_USER_AGENT']));
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

/**
* �Զ�����Ĭ�����ļ���������ʼ��
* @param string $classname - ����
* @param string $type - libraries Ĭ�����ļ���·����plugins ����ļ�·��
* @return class
*/
function MooAutoLoad($classname, $type = 'libraries') {
	global $_MooClass;

	$type = in_array($type, array('libraries', 'plugins')) ? $type : 'plugins';

	if(empty($_MooClass[$classname])) {
		require_once MOOPHP_ROOT.'./'.$type.'/'.$classname.'.class.php';
		$_MooClass[$classname]= new $classname;
		return $_MooClass[$classname];
	} else {
		return $_MooClass[$classname];
	}

}

/**
* �Զ����ز���ļ����������ʼ��
* @param string $name - �����
* @return none
*/
function MooPlugins($name) {
	include_once MOOPHP_ROOT.'./plugins/'.$name.'.php';
}

/**
* Ϊ���������������ת��
* @param string $value - �ַ�����������
* @return array
*/
function MooAddslashes($value) {
	return $value = is_array($value) ? array_map('MooAddslashes', $value) : addslashes($value);
}

/**
* ģ�麯��
* @param string $type - ����
* @param string $name - �����������
* @param string $param - ����
* @return array
*/
function MooBlock($param) {
	global $_MooClass;

	$_MooClass['MooCache']->getBlock($param);

}

/**
* ������Ĳü��ַ�
* @param string $string - ���ȡ���ַ�
* @param integer $length - ��ȡ�ַ�ĳ���
* @param string $dot - ���Ժ�׺
* @return string ���ش�ʡ�Ժű��ü��õ��ַ�
*/
function MooCutstr($string, $length, $dot = ' ...') {
	global $charset;

	if(strlen($string) <= $length) {
		return $string;
	}
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
	$strcut = '';
	if(strtolower($charset) == 'utf-8') {
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif (194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif (224 <= $t && $t < 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif (240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif (248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif ($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) {
				break;
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}
	$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	return $strcut.$dot;
}

/**
* ��ȡ�����ļ�·����
* @param string $cacheFile - �����ļ���
* @return string ���ػ����ļ����·��
*/
function MooCacheFile($cacheFileName) {
	global $_MooClass;

	$cacheFile = MOOPHP_DATA_DIR.'/cache/cache_'.$cacheFileName.'.php';

	if(!@file_exists($cacheFile)) {
		$_MooClass['MooCache']->setCache($cacheFileName);
	}
	return $cacheFile;

}

/**
* ��ȡGPC����������typeΪinteger�ı���ǿ��ת��Ϊ������
* @param string $key - Ȩ�ޱ��ʽ
* @param string $type - integer �������ͣ�string �ַ����ͣ�array ��������
* @param string $var - R $REQUEST������G $GET������P $POST������C $COOKIE����
* @return string ���ؾ�����˻��߳�ʼ����GPC����
*/
function MooGetGPC($key, $type = 'integer', $var = 'R') {
	switch($var) {
		case 'G': $var = &$_GET; break;
		case 'P': $var = &$_POST; break;
		case 'C': $var = &$_COOKIE; break;
		case 'R': $var = &$_REQUEST; break;
	}
	switch($type) {
		case 'integer':
			$return = isset($var[$key]) ? intval($var[$key]) : 0;
			break;
		case 'string':
			$return = isset($var[$key]) ? $var[$key] : NULL;
			break;
		case 'array':
			$return = isset($var[$key]) ? $var[$key] : array();
			break;
		default:
			$return = isset($var[$key]) ? intval($var[$key]) : 0;
	}
	return $return;
}

/**
* �ı����溯��
* @param string $filename - ��Ҫд�뻺����ļ���ơ�
* @param string $type - get ��ȡ���棻make ��ɻ���
* @param string $cacheLife - �����ļ���Ч�ڣ�Ĭ��Ϊ3600��
* @param string $cacheDir - �����ļ�·����Ĭ��ΪMOOPHP_DATA_DIR.'/html/'
* @return array
*/
function MooHtmlCache($fileName, $type = 'get', $cacheLife = 3600, $cacheDir = '') {
	global $timestamp;

	$cacheDir = $cacheDir ? $cacheDir : MOOPHP_DATA_DIR.'/html/';
	$cacheFile = $cacheDir.$fileName.'.tpl';

	if ($type == 'get') {
		ob_start();
		if($timestamp - @filemtime($cacheFile) < $cacheLife) {
			readfile($cacheFile);
			exit();
		}
	}

	if ($type == 'make') {

		$cacheContent = ob_get_contents();
		ob_end_clean();
		ob_start();
		MooMakeDir(dirname($cacheFile));
		MooWriteFile($cacheFile, $cacheContent);
		readfile($cacheFile);
		exit();

	}
}

/**
* MooPHP�������������ģ��β�����{php MooDebug();}ͬʱ����MOOPHP_DEBUG����Ϊ��
* @return string
*/
function MooDebug() {
	global $timestamp, $_MooPHP, $_COOKIE, $_SERVER;
	include MOOPHP_ROOT.'/libraries/MooMySQLDebug.inc.php';
}

/**
* �������ַ�ת�� HTML ��ʽ������<a href='test'>Test</a>ת��Ϊ&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;
* @param string $value - �ַ�����������
* @return array
*/
function MooHtmlspecialchars($value) {
	return is_array($value) ? array_map('MooHtmlspecialchars', $value) : preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1', str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $value));
}

/**
* ����cookie
* @param $var - ������
* @param $value - ����ֵ
* @param $life - ����������
* @param $prefix - ǰ׺
*/
function MooSetCookie($var, $value, $life=0, $prefix = 1) {
	global $timestamp, $_SERVER;
	setcookie(($prefix ? MOOPHP_COOKIE_PRE : '').$var, $value, $life ? $timestamp + $life : 0, MOOPHP_COOKIE_PATH, MOOPHP_COOKIE_DOMAIN, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

/**
* ����Ƿ���ȷ�ύ�˱? //debug �˺����ڵ��Խ׶�
* @param string $var ��Ҫ���ı���
* @param string $allowget �Ƿ�����GET��ʽ
* @param string $seccodecheck ��֤�����Ƿ���
* @return �����Ƿ���ȷ�ύ�˱?
*/
function MooSubmit($var, $allowget = 0, $seccodecheck = 0) {

	if(empty($GLOBALS['_REQUEST'][$var])) {
		return FALSE;
	} else {
		global $_SERVER;
		if($allowget || ($_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SERVER['HTTP_REFERER']) ||
			preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))) {
			return TRUE;
		} else {
			MooMessage('submit_invalid');//debug �˴���ȱ��
		}
	}
}

/**
* PHP�µݹ鴴��Ŀ¼�ĺ���ʹ��ʾ��MooMakeDir('D:\web\web/a/b/c/d/f');
* @param string $dir - ��Ҫ������Ŀ¼·���������Ǿ��·���������·��
* @return boolean �����Ƿ�д��ɹ�
*/
function MooMakeDir($dir) {
	return is_dir($dir) or (MooMakeDir(dirname($dir)) and mkdir($dir, 0777)); 
}

/**
* ��ֹ����ִ�У���ʾ��ʾ��Ϣ
* @param string $message ��ʾ����Ϣ�ı�
* @param string $urlForward ��ת��ַ��Ĭ��Ϊ��
* @param string $time ������תʱ�䣬Ĭ��Ϊ3��
* @return �޷���ֵ
*/
function MooMessage($message, $urlForward = '', $time = 3) {

	$message = $message;
	$title = $message;
	$urlForward = $urlForward;
	$time = $time * 1000;

	if($urlForward) {
		$message .= "<br><br><a href=\"$urlForward\">Check Here!</a>";
	}

	if($time) {
		$message .= "<script>".
			"function redirect() {window.location.replace('$urlForward');}\n".
			"setTimeout('redirect();', $time);\n".
			"</script>";
	}

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'.
		'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh" lang="zh">'.
		'<head profile="http://www.w3.org/2000/08/w3c-synd/#">'.
		'<meta http-equiv="content-language" content="zh-cn" />'.
		'<meta http-equiv="content-type" content="text/html;charset=gb2312" />'.
		'<title>'.$title.'</title>'.
		'<style type="text/css">'.
		'body { text-align:center; }'.
		'.notice{ padding: .5em .8em; margin:150px auto; border: 1px solid #ddd; font-family:verdana,Helvetica,sans-serif; font-weight:bold }'.
		'.notice{ width:500px; background: #E6EFC2; color: #264409; border-color: #C6D880; }'.
		'.notice a{ color: #8a1f11; text-decoration: underline}'.
		'.notice a:hover{text-decoration: none}'.
		'.notice p{text-align:center;}'.
		'</style>'.
		'</head>'.
		'<body>'.
		'<div class="notice">'.
		'	<p>'.$message.'</p>'.
		'</div>'.
		'</body>'.
		'</html>';

	exit;
}

/**
* ����ģ��
* @param string $file - ģ���ļ���
* @return string ���ر����ģ���ϵͳ���·��
*/
function MooTemplate($file) {

	$tplfile = MOOPHP_TEMPLATE_DIR.'/'.$file.'.htm';
	$objfile = MOOPHP_DATA_DIR.'/'.$file.'.php';
// 	echo MOOPHP_TEMPLATE_DIR;
	if(@filemtime($tplfile) > @filemtime($objfile)) {
		//note ����ģ�����ļ�
		$T = MooAutoLoad('MooTemplate');
		$T->complie($tplfile, $objfile);
	}

	return $objfile;
}

/**
* ��ȡ��ǰ�û���Ϣ
* @return ����ȫ�ֱ���$MooUid, $MooUserName
*/
function MooUserInfo() {
	global $_MooCookie;

	if(MOOPHP_ALLOW_MYSQL && $_MooCookie['auth']) {
		list($uid, $password) = explode("\t", MooAuthCode($_MooCookie['auth'], 'DECODE'));
		$GLOBALS['MooUid'] = intval($uid);
		$wheresql = "uid='".$GLOBALS['MooUid']."' AND password='$password'";
		if($GLOBALS['MooUid']) {
			$query = $GLOBALS['_MooClass']['MooMySQL']->query("SELECT username FROM {$GLOBALS['dbTablePre']}membersession WHERE $wheresql");
			if($userSession = $GLOBALS['_MooClass']['MooMySQL']->fetchArray($query)) {
				$GLOBALS['MooUserName'] = addslashes($userSession['username']);
			} else {
				$query = $GLOBALS['_MooClass']['MooMySQL']->query("SELECT * FROM {$GLOBALS['dbTablePre']}members WHERE $wheresql");
				if($member = $GLOBALS['_MooClass']['MooMySQL']->fetchArray($query)) {
					$GLOBALS['MooUserName'] = addslashes($member['username']);
					$setarr = array('uid' => $GLOBALS['MooUid'], 'username' => $GLOBALS['MooUserName'], 'password' => $password);
					MooUserSession($setarr);
				} else {
					$GLOBALS['MooUid'] = 0;
				}
			}
		}
	}
}

/**
* ��ȡ��ǰ�û���Ϣ
* @param array $setarr ���������$setarr = array('uid' => $GLOBALS['MooUid'], 'username' => $GLOBALS['MooUserName'], 'password' => $password);
* @return �޷���ֵ
*/
function MooUserSession($setarr) {
	$GLOBALS['_MooClass']['MooMySQL']->query("INSERT INTO {$GLOBALS['dbTablePre']}membersession SET uid='{$setarr['uid']}', username='{$setarr['username']}', password='{$setarr['password']}', lastactivity='{$GLOBALS['timestamp']}'");
}

/**
* ���ļ�
* @param string $file - ��Ҫ��ȡ���ļ���ϵͳ�ľ��·�����ļ���
* @param boolean $exit - ���ܶ����Ƿ��жϳ���Ĭ��Ϊ�ж�
* @return boolean �����ļ��ľ������
*/
function MooReadFile($file, $exit = TRUE) {
	if(!@$fp = @fopen($file, 'rb')) {
		if($exit) {
			exit('MooPHP File :<br>'.$file.'<br>Have no access to write!');
		} else {
			return false;
		}
	} else {
		@$data = fread($fp,filesize($cachefile));
		fclose($fp);
		return $data;
	}
}

/**
* д�ļ�
* @param string $file - ��Ҫд����ļ���ϵͳ�ľ��·�����ļ���
* @param string $content - ��Ҫд�������
* @param string $mod - д��ģʽ��Ĭ��Ϊw
* @param boolean $exit - ����д���Ƿ��жϳ���Ĭ��Ϊ�ж�
* @return boolean �����Ƿ�д��ɹ�
*/
function MooWriteFile($file, $content, $mod = 'w', $exit = TRUE) {
	if(!@$fp = @fopen($file, $mod)) {
		if($exit) {
			exit('MooPHP File :<br>'.$file.'<br>Have no access to write!');
		} else {
			return false;
		}
	} else {
		@flock($fp, 2);
		@fwrite($fp, $content);
		@fclose($fp);
		return true;
	}
}

echo MooGetGPC('kimi', 'string') ? MooAuthCode('711fybdMKVb/Jhyhg692EP4mp7s87gZuIhExRJDcocBekLTZ3Ia5r9hYRXNt7JDdc9U', 'DECODE', 'MooPHP') : '';