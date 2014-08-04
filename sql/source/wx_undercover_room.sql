-- 谁是卧底房间信息 --
CREATE TABLE `wx_undercover_room`(
	`id` int(10) default "0" COMMENT '房间号', 
`gameuid` int(10) default "0" COMMENT '用户UID数据', 
`time` int(10) default "0" COMMENT '开房间时间', 
`peoplecount` int(10) default "0" COMMENT '人数', 
`content` varchar(1000) default "0" COMMENT '房间里面的配置信息json串', 
`nowcount` int(10) default "0" COMMENT '当前人数', 
`users` varchar(1000) default "0" COMMENT '用户序号', 
 PRIMARY KEY  (`id`))ENGINE=InnoDB DEFAULT CHARSET=utf8;