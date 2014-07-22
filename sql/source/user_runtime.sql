-- 用户正在生产的信息表 --
CREATE TABLE `user_runtime`(
	`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`data` varchar(500) default "[]" COMMENT 'Runtime数据', 
`updatetime` int(10) default "0" COMMENT '更新时间', 
 PRIMARY KEY  (`gameuid`))ENGINE=InnoDB DEFAULT CHARSET=utf8;