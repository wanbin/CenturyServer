<?php
//读某个人的信息，把这个人的未读标记去掉
include_once PATH_HANDLER . 'GameHandler.php';
$gameid=$_REQUEST['gameid'];

$game = new GameHandler ( $uid );
echo $game->like ( $gameid );

