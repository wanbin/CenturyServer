-- 游戏房间 --
CREATE TABLE `user_rooms`(
	`id` int(10) default "0" COMMENT 'ID', 
`gameuid` int(10) default "0" COMMENT '添加人gameuid', 
`roomid` int(10) default "0" COMMENT '所在房间ID', 
`content` varchar(200) default "0" COMMENT '发送的内容', 
`updatetime` int(10) default "" COMMENT '最后更新时间', 
 PRIMARY KEY  (`id`))ENGINE=InnoDB DEFAULT CHARSET=utf8;