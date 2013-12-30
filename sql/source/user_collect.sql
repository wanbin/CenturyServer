-- 用户收藏表、点赞 --
CREATE TABLE `user_collect`(
	`user_id` int(10) default "0" COMMENT '用户ID', 
`publish_id` int(10) default "0" COMMENT '真心话大冒险ID', 
`type` int(10) default "0" COMMENT '1收藏,2喜欢，3不喜欢', 
`time` int(10) default "0" COMMENT '收藏时间' )ENGINE=InnoDB DEFAULT CHARSET=utf8;