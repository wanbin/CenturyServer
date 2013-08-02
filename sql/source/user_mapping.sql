-- 用户Mapping映射表 --
CREATE TABLE `user_mapping`(
`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`uid` varchar(100) COMMENT '用户UID数据', 
 PRIMARY KEY  (`gameuid`), UNIQUE KEY `uid` (`uid`))ENGINE=InnoDB
 DEFAULT CHARSET=utf8;