<?php 
//用户正在生产的信息表 
define("MEMCACHE_KEY_USERINVADEREWARD", "centurywar_UserInvadeReward_%d");
define("MEMCACHE_KEY_USERINVADEREWARD_LIST", "centurywar_UserInvadeReward_list_%d");

//用户限时BOSS表 
define("MEMCACHE_KEY_LIMITBOSS", "centurywar_LimitBoss_%d");
define("MEMCACHE_KEY_LIMITBOSS_LIST", "centurywar_LimitBoss_list_%d");

//微信用户表 
define("MEMCACHE_KEY_UNDERCOVER", "centurywar_UnderCover_%d");
define("MEMCACHE_KEY_UNDERCOVER_LIST", "centurywar_UnderCover_list_%d");

//谁是卧底房间信息 
define("MEMCACHE_KEY_UNDERCOVERROOM", "centurywar_UnderCoverRoom_%d");
define("MEMCACHE_KEY_UNDERCOVERROOM_LIST", "centurywar_UnderCoverRoom_list_%d");

//惩罚与真心话 
define("MEMCACHE_KEY_PUBLISH", "centurywar_Publish_%d_%d");
define("MEMCACHE_KEY_PUBLISH_LIST", "centurywar_Publish_list_%d");

//用户收藏表、点赞 
define("MEMCACHE_KEY_COLLECT", "centurywar_Collect_%d_%d");
define("MEMCACHE_KEY_COLLECT_LIST", "centurywar_Collect_list_%d");

//用户行为LOG 
define("MEMCACHE_KEY_BEHAVE", "centurywar_Behave_%d_%d");
define("MEMCACHE_KEY_BEHAVE_LIST", "centurywar_Behave_list_%d");

//用户行为LOG 
define("MEMCACHE_KEY_MAIL", "centurywar_Mail_%d_%d");
define("MEMCACHE_KEY_MAIL_LIST", "centurywar_Mail_list_%d");

//谁是卧底词汇 
define("MEMCACHE_KEY_WORDS", "centurywar_Words_%d_%d");
define("MEMCACHE_KEY_WORDS_LIST", "centurywar_Words_list_%d");

//游戏房间 
define("MEMCACHE_KEY_ROOMS", "centurywar_Rooms_%d_%d");
define("MEMCACHE_KEY_ROOMS_LIST", "centurywar_Rooms_list_%d");

define("REDIS_KEY_GAMELIKE_USER","redis_key_gamelike_user_%d");

define("REDIS_KEY_GAMELIKE_GAME","redis_gamelike_game_%d");
define("REDIS_KEY_GAMEDISLIKE_GAME","redis_gamedislike_game_%d");

//redis自增建
define ( "REDIS_KEY_ADD_ID", "redis_key_add_id_%s" );



//用户gameuid
define ( "CACHE_KEY_USER", "cache_key_user_%d" );

//惩罚缓存
define ( "CACHE_KEY_PUBLISH", "cache_key_publish_%d" );


//房间信息
define ( "CACHE_KEY_ROOMINFO", "cache_key_roominfo_%d" );

//房间信息
define ( "CACHE_KEY_ROOMUSERINFO", "cache_key_roomuserinfo_%d" );

//房间中人员信息
define ( "REDIS_KEY_ROOMUSER", "redis_key_roomuser_%d" );
