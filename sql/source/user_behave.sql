-- 用户行为LOG --
CREATE TABLE `user_behave`(
	`gameuid` int(10) default "0" COMMENT '用户ID', 
`time` int(10) default "0" COMMENT '时间', 
`behave` varchar(20) default "0" COMMENT '行为', 
`data` varchar(200) default "" COMMENT '附加信息', 
`sec` varchar(32) default "" COMMENT '加密串', 
 PRIMARY KEY  (`gameuid`,`time`,`behave`))ENGINE=InnoDB DEFAULT CHARSET=utf8;