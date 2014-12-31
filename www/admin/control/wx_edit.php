<?php
include_once PATH_HANDLER.'WXHandler.php';
$game=new WXHandler($uid);
$ret=$game->getMessageList();
