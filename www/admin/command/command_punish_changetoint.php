<?php
//读某个人的信息，把这个人的未读标记去掉
include_once PATH_HANDLER . 'PunishHandler.php';
$punish = new PunishHandler($uid);
$punish->changeTypeToInt();
echo "成功";