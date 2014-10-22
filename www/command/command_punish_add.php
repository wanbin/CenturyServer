<?php
//读某个人的信息，把这个人的未读标记去掉
$content=$_REQUEST['content'];
$gametype=$_REQUEST['gametype'];
include_once PATH_HANDLER . 'PunishHandler.php';
$punish = new PunishHandler($uid);
$arr=explode("\n", $content);
$count = 0;
foreach ( $arr as $key => $value ) {
	$content = trim ( $value );
	if (strlen ( $content ) > 0) {
		$count ++;
		$punish->newPublish ( $content, $gametype );
	}
}
echo $count;
