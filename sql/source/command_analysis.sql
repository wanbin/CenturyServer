-- 方法性能分析 --
CREATE TABLE `command_analysis`(
	`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`command` varchar(100) default "[]" COMMENT '', 
`createtime` int(10) default "0" COMMENT '', 
`type` varchar(15) default "" COMMENT '', 
`count` int(10) default "0" COMMENT '' )ENGINE=InnoDB DEFAULT CHARSET=utf8;