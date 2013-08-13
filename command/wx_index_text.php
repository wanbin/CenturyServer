<?php
/**
  * wechat php test
  */
include_once '../Entry.php';
$fromUsername="uHjQjsqWDtru-GeaV13nAtd0dh8";
$fromUsername.=886812;
// $fromUsername.=rand(1,10000);
include_once PATH_DATAOBJ . "/cache/UnderCoverCache.php";
$UnderCache = new UnderCoverCache ( $fromUsername );
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> ';
echo str_replace("\n", "<br/>",  $UnderCache->returncontent ( $_REQUEST['commad'] ));

