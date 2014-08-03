<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.
	常用函数
	$Id: Global.function.php 366 2008-07-11 08:23:25Z guorc $
*/


/**
 * 文本转HTML
 *
 * @param string $txt;
 * return string;
 */
function Text2Html($txt){
	$txt = str_replace("  ","　",$txt);
	$txt = str_replace("<","&lt;",$txt);
	$txt = str_replace(">","&gt;",$txt);
	$txt = preg_replace("/[\r\n]{1,}/isU","<br/>\r\n",$txt);
	return $txt;
}

/**
 * 获得IP
 * return string;
 */
function GetIP(){
	if(!empty($_SERVER["HTTP_CLIENT_IP"])) { $cip = $_SERVER["HTTP_CLIENT_IP"]; }
	else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) { $cip = $_SERVER["HTTP_X_FORWARDED_FOR"]; }
	else if(!empty($_SERVER["REMOTE_ADDR"])) { $cip = $_SERVER["REMOTE_ADDR"]; }
	else $cip = "";
	preg_match("/[\d\.]{7,15}/", $cip, $cips);
	$cip = $cips[0] ? $cips[0] : 'unknown';
	unset($cips);
	return $cip;
}

/**
*分页函数
*
*
*/
function multi($total, $perPage, $curPage, $pageUrl, $maxPages = 0, $page = 10, $autoGoTo = TRUE, $simple = FALSE) {
	$multiPage = '';
	$pageUrl .= strpos($pageUrl, '?') ? '&amp;' : '?';
	$realPages = 1;
	if($total > $perPage) {
		$offset = 2;

		$realPages = @ceil($total / $perPage);
		$pages = $maxPages && $maxPages < $realPages ? $maxPages : $realPages;

		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curPage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curPage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif ($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}

		$multiPage = ($curPage - $offset > 1 && $pages > $page ? '<a href="'.$pageUrl.'page=1" class="first"'.$ajaxtarget.'>1 ...</a>' : '').
			($curPage > 1 && !$simple ? '<a href="'.$pageUrl.'page='.($curPage - 1).'" class="prev"'.$ajaxtarget.'>&lsaquo;&lsaquo;</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multiPage .= $i == $curPage ? '<strong>'.$i.'</strong>' :
				'<a href="'.$pageUrl.'page='.$i.($ajaxtarget && $i == $pages && $autoGoTo ? '#' : '').'"'.$ajaxtarget.'>'.$i.'</a>';
		}

		$multiPage .= ($curPage < $pages && !$simple ? '<a href="'.$pageUrl.'page='.($curPage + 1).'" class="next"'.$ajaxtarget.'>&rsaquo;&rsaquo;</a>' : '').
			($to < $pages ? '<a href="'.$pageUrl.'page='.$pages.'" class="last"'.$ajaxtarget.'>... '.$realPages.'</a>' : '').
			(!$simple && $pages > $page && !$ajaxtarget ? '<kbd><input type="text" name="custompage" size="3" onkeydown="if(event.keyCode==13) {window.location=\''.$pageUrl.'page=\'+this.value; return false;}" /></kbd>' : '');

		$multiPage = $multiPage ? '<div class="pages">'.(!$simple ? '<em>&nbsp;'.$total.'&nbsp;</em>' : '').$multiPage.'</div>' : '';
	}
	$maxpage = $realPages;
	return $multiPage;
}

/**
 *
 * 生成GUID
 *
 * @return string 
 *
 **/
function getGUID(){
		global $_MooClass;
		$result = $_MooClass['MooMySQL']->getOne("select uuid() as GUID");
		return $result['GUID'];
}

/**
 *
 * 获得用户的GUID
 * 描述：获得第三方用户的GUID
 *
 * @param string $app_uid;
 * @param string $app_username;
 * @param integer $type;
 * @return string 
 * 
 **/
function systemGetGUID($app_uid,$app_username,$app_type=0){
		global $_MooClass;
		$user = $_MooClass['MooMySQL']->getOne("select sys_uid from system_user where app_type={$app_type} and app_uid='{$app_uid}'");
		if(!$user){
				$GUID = getGUID();
				$date = time();
				@$_MooClass['MooMySQL']->query("insert into system_user (`sys_uid`,`app_type`,`app_uid`,`app_username`,`sys_date`) values ('{$GUID}',{$app_type},'{$app_uid}','{$app_username}','{$date}')");
				return $GUID;
		}else{
				return $user['sys_uid'];
		}
}