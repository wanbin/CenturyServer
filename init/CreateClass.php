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
);
?>