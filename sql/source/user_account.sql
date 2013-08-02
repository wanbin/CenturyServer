-- 用户基本信息表 --
CREATE TABLE `user_account`(
	`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`server` int(5) default "0" COMMENT '玩家当前等级', 
`uid` varchar(100) default "" COMMENT '玩家UID', 
`exp` int(10) default "0" COMMENT '玩家当前经验', 
`level` int(5) default "0" COMMENT '玩家当前等级', 
`createtime` int(10) default "0" COMMENT '玩家创建时间 ', 
`updatetime` int(10) default "0" COMMENT '玩家更新时间 ', 
`power` int(5) default "0" COMMENT '玩家体力', 
`ip` varchar(20) default "" COMMENT '玩家UID', 
`country` varchar(10) default "" COMMENT '玩家UID', 
`authcode` int(6) default "0" COMMENT '玩家UID', 
 PRIMARY KEY  (`gameuid`))ENGINE=InnoDB DEFAULT CHARSET=utf8;