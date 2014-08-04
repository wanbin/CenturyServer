-- 微信用户表 --
CREATE TABLE `wx_account`(
	`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`uid` varchar(100) default "" COMMENT '用户UID数据', 
`lastlogin` int(10) default "0" COMMENT '最后登录时间', 
 PRIMARY KEY  (`gameuid`))ENGINE=InnoDB DEFAULT CHARSET=utf8;
 -- 微信用户发信息Log --
CREATE TABLE `wx_log`(
`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`time` int(10) default "0" COMMENT '最后发信息时间', 
`content` varchar(1000) default "" COMMENT '用户发来信息')ENGINE=InnoDB DEFAULT CHARSET=utf8;