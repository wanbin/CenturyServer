-- 测试数据库连接 --
CREATE TABLE `testContent`(
	`gameuid` int(10) default "0" COMMENT '', 
`count` int(10) default "0" COMMENT '', 
 PRIMARY KEY  (`gameuid`,`count`))ENGINE=InnoDB DEFAULT CHARSET=utf8;