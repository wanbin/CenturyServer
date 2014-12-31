<?php
//读某个人的信息，把这个人的未读标记去掉
$keyword=$_REQUEST["key"];
$content=$_REQUEST["content"];

include_once PATH_HANDLER.'WXHandler.php';
$game=new WXHandler($uid);
$ret=$game->updateReturn($keyword, $content);

echo "更新关键词 $keyword 成功";
