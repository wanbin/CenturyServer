<?php 
$showContent=$_REQUEST['page'];
include_once PATH_HANDLER.'PageHandler.php';
$page=new PageHandler($uid);
if(empty($showContent)){
	$showContent='INDEX';
}
$ret=$page->getPageFromKey($showContent);
?>