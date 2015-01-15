<?php
//读某个人的信息，把这个人的未读标记去掉
$id = $_REQUEST ['id'];
include_once PATH_HANDLER . 'GuessHandler.php';
$words = new GuessHandler ( $uid );
$words->delWords ( $id);

echo "删除成功" . rand ( 1, 900 );