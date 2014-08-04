<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: MooCacheConfig.php 271 2008-06-01 08:05:46Z kimi $
*/

$_MooCacheConfig = array
		(
		'index'		=> array('settings', 'cacheKey'),
		'category'		=> array('category'),
		'cacheFile'		=> array('cacheKey')
		);

function MooGetCache_cacheKey() {

	return $GLOBALS['_MooClass']['MooMySQL']->getAll("SELECT * FROM moophp_test WHERE id=id ORDER BY id DESC LIMIT 0, 5");

}

function MooGetCache_settings() {

	return $GLOBALS['_MooClass']['MooMySQL']->getAll("SELECT * FROM moophp_settings");
}

function MooGetCache_category() {
	global $dbTablePre;

	$query = $GLOBALS['_MooClass']['MooMySQL']->query("SELECT * FROM {$dbTablePre}categories");
	$cateArray = array();
	while ($category = $GLOBALS['_MooClass']['MooMySQL']->fetchArray($query)) {
		$cateArray[$category['cateid']] = $category;
	}

	return $cateArray;
}