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
		array(
				'modelname' => 'Building',
				'singleData' => true,
				'tablename' => 'user_building',
				'tablefiled' => array (
						'gameuid' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>1,'isjson'=>0,'comment'=>'玩家GAMEUID'),
						'crop' => array('type'=>'int','lengh'=>5,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'资源地1'),
						'cropupdatetime' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'收获时间'),
				),
				'description' => '用户建筑表',
		),
		array(
				'modelname' => 'Runtime',
				'singleData' => true,
				'tablename' => 'user_runtime',
				'tablefiled' => array (
						'gameuid' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>1,'isjson'=>0,'comment'=>'玩家GAMEUID'),
						'data' => array('type'=>'varchar','lengh'=>500,'default'=>'[]','isPrimaryKey'=>0,'isjson'=>1,'comment'=>'Runtime数据'),
						'updatetime' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'更新时间'),
				),
				'description' => '用户正在生产的信息表',
		),
		array(
				'modelname' => 'Mapping',
				'singleData' => true,
				'tablename' => 'user_mapping',
				'tablefiled' => array (
						'gameuid' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>1,'isjson'=>0,'comment'=>'玩家GAMEUID'),
						'uid' => array('type'=>'varchar','lengh'=>100,'default'=>'','isPrimaryKey'=>0,'isjson'=>0,'comment'=>'用户UID数据'),
				),
				'description' => '用户Mapping映射表',
		),
		
		array(
				'modelname' => 'UserInvadeReward',
				'singleData' => true,
				'tablename' => 'user_invite_reward',
				'tablefiled' => array (
						'gameuid' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>1,'isjson'=>0,'comment'=>'玩家GAMEUID'),
						'reward' => array('type'=>'varchar','lengh'=>500,'default'=>'[]','isPrimaryKey'=>0,'isjson'=>1,'comment'=>'Runtime数据'),
						'updatetime' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'更新时间'),
				),
				'description' => '用户正在生产的信息表',
		),
		
		array(
				'modelname' => 'LimitBoss',
				'singleData' => true,
				'tablename' => 'user_limit_boss',
				'tablefiled' => array (
						'gameuid' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>1,'isjson'=>0,'comment'=>'玩家GAMEUID'),
						'data' => array('type'=>'varchar','lengh'=>5000,'default'=>'[]','isPrimaryKey'=>0,'isjson'=>1,'comment'=>'Runtime数据'),
						'endtime' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'创建时间 '),
						'boss' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'boss编号'),
						'level' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'等级'),
						'soldier' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'剩余兵数'),
						'updatetime' => array('type'=>'int','lengh'=>10,'default'=>0,'isPrimaryKey'=>0,'isjson'=>0,'comment'=>'更新时间'),
				),
				'description' => '用户限时BOSS表',
		),
);
?>