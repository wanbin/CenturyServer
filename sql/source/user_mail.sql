-- 用户行为LOG --
CREATE TABLE `user_mail`(
`id` int(10) AUTO_INCREMENT COMMENT 'ID', 
`gameuid` int(10) default "0" COMMENT '收件人gameuid', 
`fromgameuid` int(10) default "0" COMMENT '发信人gameuid', 
`time` int(10) default "0" COMMENT '发信时间', 
`content` varchar(200) default "" COMMENT '邮件信息', 
`is_read` int(10) default "" COMMENT '是否已读', 
 PRIMARY KEY  (`id`))ENGINE=InnoDB DEFAULT CHARSET=utf8;