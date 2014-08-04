<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: Mysql.Class.php 2008-3-19 aming$
*/


!defined('IN_MOOPHP') && exit('Access Denied');


if(MOOPHP_DEBUG) {
	print<<<EOF
	<style>
	.moodebugtable, .moodebugtable2 {
	text-align:left;width:1000px;border:0;border-collapse:collapse;margin-bottom:15px;table-layout: fixed; word-wrap: break-word;background:#FFF;}
	.moodebugtable table, .moodebugtable2 table {width:100%;border:0;table-layout: fixed; word-wrap: break-word;}
	.moodebugtable table td, .moodebugtable2 table td {border-bottom:0;border-right:0;border-color: #ADADAD;}
	.moodebugtable th, .moodebugtable2 th {border:1px solid #000;background:#CCC;padding: 2px;font-size: 12px;}
	.moodebugtable td, .moodebugtable2 td {border:1px solid #000;background:#EFF5D9;padding: 2px;font-size: 12px;}
	.moodebugtable2 th {background:#E5EAD1;}
	.moodebugtable2 td {background:#FFFFFF;}
	.firsttr td {border-top:0;}
	.firsttd {border-left:none !important;}
	.bold {font-weight:bold;}
	</style>
	<div id="MooPHP_debug" style="display:;">
EOF;
	$class = 'moodebugtable2';
	if(empty($_MooPHP['debug_query'])) $_MooPHP['debug_query'] = array();
	foreach ($_MooPHP['debug_query'] as $key => $debug) {
		($class == 'moodebugtable')?$class = 'moodebugtable2':$class = 'moodebugtable';
		echo '<table cellspacing="0" class="'.$class.'"><tr><th rowspan="2" width="20">'.($key+1).'</th><td width="60">'.$debug['time'].' ms</td><td class="bold">'.MooHtmlspecialchars($debug['sql']).'</td></tr>';
		if(!empty($debug['info'])) {
			echo '<tr><td>Info</td><td>'.$debug['info'].'</td></tr>';
		}
		if(!empty($debug['explain'])) {
			echo '<tr><td>Explain</td><td><table cellspacing="0"><tr class="firsttr"><td width="5%" class="firsttd">id</td><td width="10%">type</td><td width="12%">table</td><td width="5%">type</td><td width="20%">possible_keys</td><td width="10%">key</td><td width="8%">key_len</td><td width="5%">ref</td><td width="5%">rows</td><td width="20%">Extra</td></tr><tr>';
			foreach ($debug['explain'] as $key => $explain) {
				($key == 'id')?$tdclass = ' class="firsttd"':$tdclass='';
				if(empty($explain)) $explain = '-';
				echo '<td'.$tdclass.'>'.$explain.'</td>';
			}
			echo '</tr></table></td></tr>';
		}
		echo '</table>';
	}
	if($values = $_GET) {
		($class == 'moodebugtable')?$class = 'moodebugtable2':$class = 'moodebugtable';
		$i = 1;
		echo '<table class="'.$class.'">';
			foreach ($values as $key => $get) {
				echo '<tr><th width="20">'.$i.'</th><td width="250">$_GET[\''.$key.'\']</td><td>'.$get.'</td></tr>';
				$i++;
			}
		echo '</table>';
	}
	if($values = $_POST) {
		($class == 'moodebugtable')?$class = 'moodebugtable2':$class = 'moodebugtable';
		$i = 1;
		echo '<table class="'.$class.'">';
			foreach ($values as $key => $post) {
				echo '<tr><th width="20">'.$i.'</th><td width="250">$_post[\''.$key.'\']</td><td>'.$post.'</td></tr>';
				$i++;
			}
		echo '</table>';
	}
	if($values = $_COOKIE) {
		($class == 'moodebugtable')?$class = 'moodebugtable2':$class = 'moodebugtable';
		$i = 1;
		echo '<table class="'.$class.'">';
			foreach ($values as $key => $cookie) {
				echo '<tr><th width="20">'.$i.'</th><td width="250">$_COOKIE[\''.$key.'\']</td><td>'.$cookie.'</td></tr>';
				$i++;
			}
		echo '</table>';
	}
	if($files = get_included_files()) {
		($class == 'moodebugtable')?$class = 'moodebugtable2':$class = 'moodebugtable';
		echo '<table class="'.$class.'">';
			foreach ($files as $key => $file) {
				echo '<tr><th width="20">'.($key+1).'</th><td>'.$file.'</td></tr>';
			}
		echo '</table>';
	}
	if($values = $_SERVER) {
		($class == 'moodebugtable')?$class = 'moodebugtable2':$class = 'moodebugtable';
		$i = 1;
		echo '<table class="'.$class.'">';
			foreach ($values as $key => $server) {
				if(!in_array($key,array('HTTP_COOKIE'))) {
					echo '<tr><th width="20">'.$i.'</th><td width="250">$_SERVER[\''.$key.'\']</td><td>'.$server.'</td></tr>';
					$i++;
				}
			}
		echo '</table>';
	}
	echo '</div>';
}