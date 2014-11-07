<?php 
global $partyGameArr;
$week=date("w",time());
//时间戳的第几周
$weekcount=ceil((time()-86400*3)/86400/7);
// echo $weekcount;
$base=2339;
$weekshowindex=$weekcount-$base;
$ret=$partyGameArr[$weekshowindex];
$ret2=$partyGameArr[$weekshowindex+1];
// print_R($ret);
if(!empty($uid)){
	include_once PATH_HANDLER . 'GameHandler.php';
	$game = new GameHandler ($uid);
	$likeInfo=$game->getLikeInfo($weekshowindex);
}

?>
