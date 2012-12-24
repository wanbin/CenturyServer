<?php
//该文件内容不会自动更新

//方法性能分析
define("MEMCACHE_KEY_COMMANDANALYSIS", "goe_commandAnalysis_%d");
define("MEMCACHE_KEY_COMMANDANALYSIS_LIST", "goe_commandAnalysis_list_%d");

//测试数据库连接
define("MEMCACHE_KEY_TESTCONTENT", "goe_testContent_%d_%d");
define("MEMCACHE_KEY_TESTCONTENT_LIST", "goe_testContent_list_%d");

//用户基本信息表
define("MEMCACHE_KEY_ACCOUNT", "goe_Account_%d");
define("MEMCACHE_KEY_ACCOUNT_LIST", "goe_Account_list_%d");

//Chat 聊天数据节点
define('MEMCACHE_KEY_NODE_SERVER_GAMEUID_POSITION', 'goe_chat_node_%d_%d_%d');
//Chat 聊天指针
define('MEMCACHE_KEY_SERVER_GAMEUID_POINTER', 'goe_chat_pointer_%d_%d');
//Chat 免费余量
define('MEMCACHE_KEY_SURPLUS_SERVER_GAMEUID', 'goe_chat_surplus_%d_%d');

