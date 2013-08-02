-- 用户建筑表 --
CREATE TABLE `user_building`(
	`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`crop` int(5) default "0" COMMENT '资源地1', 
`cropupdatetime` int(10) default "0" COMMENT '收获时间', 
 PRIMARY KEY  (`gameuid`))ENGINE=InnoDB DEFAULT CHARSET=utf8;