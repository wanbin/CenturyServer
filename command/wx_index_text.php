<?php
/**
  * wechat php test
  */
include_once '../define.php';
$fromUsername="uHjQjsqWDtru-GeaV13nAtd0dh8";
$fromUsername.=886812;
// $fromUsername.=rand(1,10000);
set_time_limit(6);

//这是第二个人
// $fromUsername="uHjQjsqWDtru-GeaV13nAtd0dh822";
// $fromUsername="uHjQjsqWDtru-GeaV13nAtd0dh823";
// $fromUsername="uHjQjsqWDtru-GeaV13nAtd0dh824";
// $fromUsername="uHjQjsqWDtru-GeaV13nAtd0dh825";

include_once PATH_DATAOBJ . "/cache/UnderCoverCache.php";
$UnderCache = new UnderCoverCache ( $fromUsername );
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> ';
echo str_replace("\n", "<br/>",  $UnderCache->returncontent ( $_REQUEST['commad'] ));

