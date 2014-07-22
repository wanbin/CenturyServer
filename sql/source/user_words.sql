-- 谁是卧底词汇 --
CREATE TABLE `user_words`(
	`id` int(10) default "0" COMMENT 'ID', 
`gameuid` int(10) default "0" COMMENT '添加人gameuid', 
`word1` varchar(10) default "0" COMMENT '词汇1', 
`word2` varchar(10) default "0" COMMENT '词汇2', 
`type` int(10) default "" COMMENT '类型', 
`status` int(10) default "" COMMENT '是否审核通过', 
`time` int(10) default "" COMMENT '添加时间', 
 PRIMARY KEY  (`id`))ENGINE=InnoDB DEFAULT CHARSET=utf8;