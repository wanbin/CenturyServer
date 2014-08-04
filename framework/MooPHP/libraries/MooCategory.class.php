<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: MooCategory.class.php 296 2008-06-03 03:22:26Z kimi $
*/


!defined('IN_MOOPHP') && exit('Access Denied');

class MooCategory {

	//note 数据库处理类
	var $dbClass = '';

	//note 缓存类
	var $cacheClass = '';

	//note 数据库名称
	var $dbName = 'moophp_categories';
	
	//note 所有分类列表数组
	var $cateArray = array();

	/**
	 * 取得所有分类信息列表
	 *
	 * @return array
	 */
	function getCateArray() {
		if(!$this->dbClass) {
			$this->dbClass = MooAutoLoad('MooMySQL');
		}

		$cateArray = array();
		$query = $this->dbClass->query("SELECT * FROM {$this->dbName}");
		while ($category = $this->dbClass->fetchArray($query)) {
			$cateArray[$category['cateid']] = $category;
		}

		return $cateArray;
	}

}
