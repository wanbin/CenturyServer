-- 游戏房间 --
CREATE TABLE `room`(
`gameuid` int(10) default "0" COMMENT '创建人gameuid', 
`name` varchar(10) default "" COMMENT '简短描述', 
`type` int(10) default "0" COMMENT '所在房间ID',
`maxcount` int(10) default "0" COMMENT '最大人数',
`nowcount` int(10) default "0" COMMENT '当前人数', 
`des` varchar(200) default "" COMMENT '房间描述', 
`content` varchar(1000) default "" COMMENT '房间描述', 
`createtime` int(10)  COMMENT '最后更新时间',
 PRIMARY KEY  (`gameuid`))ENGINE=InnoDB DEFAULT CHARSET=utf8;