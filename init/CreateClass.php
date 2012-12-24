<?php 
$author = 'WanBin';
$date = date ( 'Y-m-d', time () );
$createarray=array(
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
				'tablefiled' => array ('gameuid' => array('type'=>'int','lengh'=>10,'default'=>0,'comment'=>'玩家GAMEUID'),
						'command'=>array('type'=>'varchar','lengh'=>100,'default'=>'[]'),
						'createtime'=>array('type'=>'int','lengh'=>10,'default'=>0),
						'type' => array('type'=>'varchar','lengh'=>15,'default'=>''),
						'count' => array('type'=>'int','lengh'=>10,'default'=>0),
				),
				'description' => '方法性能分析',
		),

		array(
				'modelname' => 'testContent',
				'singleData' => false,
				'tablename' => 'testContent',
				'tablefiled' => array ('gameuid' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>1,'isjson'=>0),
						'count' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>1,'isjson'=>1),
				),
				'description' => '测试数据库连接',
		),
		
		array(
				'modelname' => 'Account',
				'singleData' => true,
				'tablename' => 'user_account',
				'tablefiled' => array (
						'gameuid' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>1,'isjson'=>0,'comment'=>'玩家GAMEUID'),
						'server' => array('type'=>'int','lengh'=>5,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'玩家当前等级'),
						'uid' => array('type'=>'varchar','lengh'=>100,'default'=>'','isPrimaryKey'=>0,'isjson'=>0,'comment'=>'玩家UID'),
						'exp' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'玩家当前经验'),
						'level' => array('type'=>'int','lengh'=>5,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'玩家当前等级'),
						'createtime' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'玩家创建时间 '),
						'updatetime' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'玩家更新时间 '),
						'power' => array('type'=>'int','lengh'=>5,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'玩家体力'),
						'ip' => array('type'=>'varchar','lengh'=>20,'default'=>'','isPrimaryKey'=>0,'isjson'=>0,'comment'=>'玩家UID'),
						'country' => array('type'=>'varchar','lengh'=>10,'default'=>'','isPrimaryKey'=>0,'isjson'=>0,'comment'=>'玩家UID'),
						'authcode' => array('type'=>'int','lengh'=>6,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'玩家UID'),
				),
				'description' => '用户基本信息表',
		),
);
?>