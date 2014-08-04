<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: MooAdminTemplateGetHtml.inc.php 405 2008-11-26 02:25:35Z kimi $
*/

!defined('IN_MOOPHP') && exit('Access Denied');


$adminFooterHtml = <<< EOT
</td></tr></table>
	<br /><br /><div class="footer"><hr size="0"  width="80%">
	Powered by <a href="http://www.moophp.org" target="_blank"><b>MooPHP</b></a> {$param['version']} &nbsp;&copy; 2007-2008  <a href="http://www.ismole.net" target="_blank"><b>IsMole.Inc.</b></a></div>
</body>
</html>
EOT;

$adminHeaderHtml = <<< EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$GLOBALS['charset']}">
<link href="./{$param['adminDir']}/images/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">var IMGDIR = '';var attackevasive = '';</script>
<script src="./{$param['adminDir']}/javascript/admin.js" type="text/javascript"></script>
<script type="text/javascript">
function checkalloption(form, value) {
	for(var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.value == value && e.type == 'radio' && e.disabled != true) {
			e.checked = true;
		}
	}
}

function checkallvalue(form, value, checkall) {
	var checkall = checkall ? checkall : 'chkall';
	for(var i = 0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.type == 'checkbox' && e.value == value) {
			e.checked = form.elements[checkall].checked;
		}
	}
}

function zoomtextarea(objname, zoom) {
	zoomsize = zoom ? 10 : -10;
	obj = \$(objname);
	if(obj.rows + zoomsize > 0 && obj.cols + zoomsize * 3 > 0) {
		obj.rows += zoomsize;
		obj.cols += zoomsize * 3;
	}
}

function redirect(url) {
	window.location.replace(url);
}

var collapsed = getcookie('Moo-collapse');
function collapse_change(menucount) {
	if(\$('menu_' + menucount).style.display == 'none') {
		\$('menu_' + menucount).style.display = '';collapsed = collapsed.replace('[' + menucount + ']' , '');
		\$('menuimg_' + menucount).src = './{$param['adminDir']}/images/menu_reduce.gif';
	} else {
		\$('menu_' + menucount).style.display = 'none';collapsed += '[' + menucount + ']';
		\$('menuimg_' + menucount).src = './{$param['adminDir']}/images/menu_add.gif';
	}
	setcookie('Moo-collapse', collapsed, 2592000);
}
</script>
</head>

<body leftmargin="10" topmargin="10">
<div id="append_parent"></div>
<table width="100%" border="0" cellpadding="2" cellspacing="6"><tr><td>
EOT;

$frameHtml = <<< EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html><head>
<title>Admin Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset={$GLOBALS['charset']}">
<script src="./{$param['adminDir']}/javascript/admin.js" type="text/javascript"></script>
</head>
<body style="margin: 0px" scroll="no">
<div style="position: absolute;top: 0px;left: 0px; z-index: 2;height: 65px;width: 100%">
<iframe frameborder="0" id="header" name="header" src="{$param['topUrl']}" scrolling="no" style="height: 65px; visibility: inherit; width: 100%; z-index: 1;"></iframe>
</div>
<table border="0" cellPadding="0" cellSpacing="0" height="100%" width="100%" style="table-layout: fixed;">
<tr><td width="165" height="65"></td><td></td></tr>
<tr>
<td><iframe frameborder="0" id="menu" name="menu" src="{$param['leftUrl']}" scrolling="yes" style="height: 100%; visibility: inherit; width: 100%; z-index: 1;overflow: auto;"></iframe></td>
<td><iframe frameborder="0" id="main" name="main" src="{$param['mainUrl']}" scrolling="yes" style="height: 100%; visibility: inherit; width: 100%; z-index: 1;overflow: auto;"></iframe></td>
</tr></table>
</body>
</html>
EOT;

$leftHtml = <<<EOT
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<title>leftMenu</title>
			<meta http-equiv="Content-Type" content="text/html; charset={$GLOBALS['charset']}">
			<link rel="stylesheet" type="text/css" id="css" href="./{$param['adminDir']}/images/admin.css">
			<script src="./{$param['adminDir']}/javascript/admin.js" type="text/javascript"></script>
			<script>
			var collapsed = getcookie("Moo-collapse");
			function collapse_change(menucount) {
				if(\$('menu_' + menucount).style.display == 'none') {
					\$('menu_' + menucount).style.display = '';collapsed = collapsed.replace('[' + menucount + ']' , '');
					\$('menuimg_' + menucount).src = './{$param['adminDir']}/images/menu_reduce.gif';
				} else {
					\$('menu_' + menucount).style.display = 'none';collapsed += '[' + menucount + ']';
					\$('menuimg_' + menucount).src = './{$param['adminDir']}/images/menu_add.gif';
				}
				setcookie('Moo-collapse', collapsed, 2592000);
			}
			</script>
		</head>
		<body style="margin:5px!important;margin:3px;">
			{$param['menuLinks']}
		</body>
	</html>
EOT;

$topHtml = <<< EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$GLOBALS['charset']}" />
<title>topMenu</title>
<link href="./{$param['adminDir']}/images/admin.css" rel="stylesheet" type="text/css" />
<script>
var menus = new Array({$param['menuKeys']});
function togglemenu(id) {
	if(parent.menu) {
		for(k in menus) {
			if(parent.menu.document.getElementById(menus[k])) {
				parent.menu.document.getElementById(menus[k]).style.display = menus[k] == id ? "" : "none";
			}
		}
	}
}

function sethighlight(n) {
	var lis = document.getElementsByTagName("li");
	for(var i = 0; i < lis.length; i++) {
		lis[i].id = "";
	}
	lis[n].id = "menuon";
}

//togglemenu("admin_home");
</script>
</head>

<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="topmenubg">
<tr>
<td rowspan="2" width="160px">
<div class="logo">
<a href="http://www.MooPHP.org/" target="_blank"><img src="./{$param['adminDir']}/images/logo.jpg" alt="MooPHP" class="logoimg" border="0"/></a>
</div>
</td><td>
	<div class="topmenu">
		<ul>
		{$param['menuLinks']}
		</ul>
	</div>
</td></tr>
</table>
EOT;

$adminShowMessageHtml = <<<EOT
<br /><br /><br /><br /><br /><br />
<table width="500" border="0" cellpadding="0" cellspacing="0" align="center" class="tableborder">
<tr class="header"><td>{$GLOBALS['adminLang']['message']}</td></tr><tr><td class="altbg2"><div align="center">
{$param['message']}</div><br /><br />
</td></tr></table>
<br /><br /><br />
EOT;
