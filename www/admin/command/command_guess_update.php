<?php
//读某个人的信息，把这个人的未读标记去掉
$type=$_REQUEST['type'];
$content = $_REQUEST ['content'];
$id = $_REQUEST ['id'];
include_once PATH_HANDLER . 'GuessHandler.php';
$words = new GuessHandler ( $uid );
$words->updateWords ( $id, $content, $type );

echo "更新成功" . rand ( 1, 900 );