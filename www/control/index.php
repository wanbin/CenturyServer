<?php 

include_once PATH_HANDLER.'PageHandler.php';
$page=new PageHandler($uid);
$ret=$page->getPageFromKey('LINK_INDEX');
?>