<?php
//读某个人的信息，把这个人的未读标记去掉
$id=$_REQUEST["_id"];

include_once PATH_HANDLER.'ArticleHandler.php';
$game=new ArticleHandler($uid);
$game->delGame($id);
echo "删除成功,ID:".$id;