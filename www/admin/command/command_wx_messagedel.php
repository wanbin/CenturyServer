<?php
//读某个人的信息，把这个人的未读标记去掉
$key=$_REQUEST["key"];

include_once PATH_HANDLER.'WXHandler.php';
$game=new WXHandler($uid);
$ret=$game->delKey($key);

echo "删除关键词 $key 成功";
