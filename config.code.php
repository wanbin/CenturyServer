<?php
//运行成功
define("COMMAND_SUCCESS", 1000);
define("COMMAND_ENPTY", 0);
define("COMMAND_FAILE", 1001);
define("PAGECOUNT", 30);


//不能加入到自己的房间中
define("ERROR_ROOM_SELF", 1100);

//加入房间时的错误 
define("ERROR_ROOM", 1101);
//不存在相应房间
define("ERROR_ROOM_NULL", 1102);
//参与人数超过权限限制
define("ROOM_NEED_PAY", 1103);
//参与人数范围无法开启指定游戏
define("PEOPLE_COUNT_ERROR", 1104);
//用户名不符合规则（2-4字符）
define("NAME_REEOR", 1105);
//没有开启相应房间
define("ROOM_EMPTY", 1106);
//用户并不在房间中
define("GAMEUID_NOT_IN", 1107);
//错误的游戏类型
define("ERROR_GAMEID", 1108);
//错误的惩罚人员列表
define("PUNISH_ERROR", 1109);
//房间打开超过两个小时，房间超时
define("ROOM_TIME_OUT", 1110);
//用户游戏间隔过小
define("FEQ_TO_MUCH", 1111);
define("REMOVE_SELF", 1112);

$GamePeople=array(
		'game_1'=>array('name'=>'网络谁是卧底','min'=>4,'max'=>12),
		'game_2'=>array('name'=>'网络杀人游戏','min'=>6,'max'=>16),
		'game_3'=>array('name'=>'网络真心话大冒险','min'=>3,'max'=>10),
		);

$gameNeedPay=array(
		array('max'=>10,'pay'=>0),
		array('max'=>20,'pay'=>6),
		array('max'=>30,'pay'=>12),
		array('max'=>50,'pay'=>18),
		array('max'=>100,'pay'=>30),
		);
define("GAME_LIMIT_USER", false);
?>
