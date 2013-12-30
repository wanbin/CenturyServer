-- 惩罚与真心话 --
CREATE TABLE `user_publish`(
	`id` int(10) default "0" COMMENT 'id', 
`user_id` int(10) default "0" COMMENT '发表的玩家UID', 
`content` varchar(500) default "" COMMENT '真心话大冒险内容', 
`time` int(10) default "0" COMMENT '创建时间', 
`type` int(10) default "0" COMMENT '类型:1真心话，2大冒险', 
`like` int(10) default "0" COMMENT '好评次数', 
`dislike` int(10) default "0" COMMENT '差评次数', 
`show` int(10) default "0" COMMENT '是否显示', 
 PRIMARY KEY  (`id`))ENGINE=InnoDB DEFAULT CHARSET=utf8;