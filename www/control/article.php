<?php 
$articleid = $_REQUEST ['articleid'];
include_once PATH_HANDLER . 'ArticleHandler.php';
$game = new ArticleHandler ( $uid, 'WEB' );
$gamecontent=$game->getOne($articleid);
?>