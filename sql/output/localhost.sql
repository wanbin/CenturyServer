
-- 创建数据库 -- 
CREATE DATABASE `centurywar` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
use centurywar;
-- 方法性能分析 --
CREATE TABLE `command_analysis`(
	`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`command` varchar(100) default "[]" COMMENT '', 
`createtime` int(10) default "0" COMMENT '', 
`type` varchar(15) default "" COMMENT '', 
`count` int(10) default "0" COMMENT '' )ENGINE=InnoDB DEFAULT CHARSET=utf8;use centurywar;-- 测试数据库连接 --
CREATE TABLE `testContent`(
	`gameuid` int(10) default "0" COMMENT '', 
`count` int(10) default "0" COMMENT '', 
 PRIMARY KEY  (`gameuid`,`count`))ENGINE=InnoDB DEFAULT CHARSET=utf8;use centurywar;-- 用户基本信息表 --
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
 PRIMARY KEY  (`gameuid`))ENGINE=InnoDB DEFAULT CHARSET=utf8;use centurywar;-- 用户建筑表 --
CREATE TABLE `user_building`(
	`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`crop` int(5) default "0" COMMENT '资源地1', 
`cropupdatetime` int(10) default "0" COMMENT '收获时间', 
 PRIMARY KEY  (`gameuid`))ENGINE=InnoDB DEFAULT CHARSET=utf8;use centurywar;-- 用户Mapping映射表 --
CREATE TABLE `user_mapping__0`(
`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`uid` varchar(100) COMMENT '用户UID数据', 
 PRIMARY KEY  (`gameuid`), UNIQUE KEY `uid` (`uid`))ENGINE=InnoDB
 DEFAULT CHARSET=utf8;-- 用户Mapping映射表 --
CREATE TABLE `user_mapping__1`(
`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`uid` varchar(100) COMMENT '用户UID数据', 
 PRIMARY KEY  (`gameuid`), UNIQUE KEY `uid` (`uid`))ENGINE=InnoDB
 DEFAULT CHARSET=utf8;-- 用户Mapping映射表 --
CREATE TABLE `user_mapping__2`(
`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`uid` varchar(100) COMMENT '用户UID数据', 
 PRIMARY KEY  (`gameuid`), UNIQUE KEY `uid` (`uid`))ENGINE=InnoDB
 DEFAULT CHARSET=utf8;-- 用户Mapping映射表 --
CREATE TABLE `user_mapping__3`(
`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`uid` varchar(100) COMMENT '用户UID数据', 
 PRIMARY KEY  (`gameuid`), UNIQUE KEY `uid` (`uid`))ENGINE=InnoDB
 DEFAULT CHARSET=utf8;-- 用户Mapping映射表 --
CREATE TABLE `user_mapping__4`(
`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`uid` varchar(100) COMMENT '用户UID数据', 
 PRIMARY KEY  (`gameuid`), UNIQUE KEY `uid` (`uid`))ENGINE=InnoDB
 DEFAULT CHARSET=utf8;-- 用户Mapping映射表 --
CREATE TABLE `user_mapping__5`(
`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`uid` varchar(100) COMMENT '用户UID数据', 
 PRIMARY KEY  (`gameuid`), UNIQUE KEY `uid` (`uid`))ENGINE=InnoDB
 DEFAULT CHARSET=utf8;-- 用户Mapping映射表 --
CREATE TABLE `user_mapping__6`(
`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`uid` varchar(100) COMMENT '用户UID数据', 
 PRIMARY KEY  (`gameuid`), UNIQUE KEY `uid` (`uid`))ENGINE=InnoDB
 DEFAULT CHARSET=utf8;-- 用户Mapping映射表 --
CREATE TABLE `user_mapping__7`(
`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`uid` varchar(100) COMMENT '用户UID数据', 
 PRIMARY KEY  (`gameuid`), UNIQUE KEY `uid` (`uid`))ENGINE=InnoDB
 DEFAULT CHARSET=utf8;-- 用户Mapping映射表 --
CREATE TABLE `user_mapping__8`(
`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`uid` varchar(100) COMMENT '用户UID数据', 
 PRIMARY KEY  (`gameuid`), UNIQUE KEY `uid` (`uid`))ENGINE=InnoDB
 DEFAULT CHARSET=utf8;-- 用户Mapping映射表 --
CREATE TABLE `user_mapping__9`(
`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`uid` varchar(100) COMMENT '用户UID数据', 
 PRIMARY KEY  (`gameuid`), UNIQUE KEY `uid` (`uid`))ENGINE=InnoDB
 DEFAULT CHARSET=utf8;use centurywar;-- 用户正在生产的信息表 --
CREATE TABLE `user_runtime`(
	`gameuid` int(10) default "0" COMMENT '玩家GAMEUID', 
`data` varchar(500) default "[]" COMMENT 'Runtime数据', 
`updatetime` int(10) default "0" COMMENT '更新时间', 
 PRIMARY KEY  (`gameuid`))ENGINE=InnoDB DEFAULT CHARSET=utf8;