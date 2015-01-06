<?php
//读某个人的信息，把这个人的未读标记去掉
include_once PATH_HANDLER . 'CollectHandler.php';
$id=$_REQUEST['id'];
$game = new CollectHandler ($uid);

$count= $game->dislike($id);

include_once PATH_HANDLER.'PunishHandler.php';
$publish = new PunishHandler ( $uid );
$publish->like($id, $count);
echo $count;