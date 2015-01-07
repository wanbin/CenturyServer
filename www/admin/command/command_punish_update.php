<?php
//读某个人的信息，把这个人的未读标记去掉
$id = $_REQUEST ['id'];
$content = str_replace("'", '"', stripslashes($_REQUEST ['content']));
$type = $_REQUEST ['type'];
include_once PATH_HANDLER . 'PunishHandler.php';
$punish = new PunishHandler ( $uid );
$punish->updatePunish ( $id, $content, $type );
echo "更新成功".rand(1,999);