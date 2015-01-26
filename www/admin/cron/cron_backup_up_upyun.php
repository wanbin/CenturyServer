<?php
//把mongo及redis备份到upyun上
include_once '../../../define.php';
include_once FRAMEWORK.'upyun/upyun.class.php';
$upyun = new UpYun('centurywarfile', 'wan', 'wanbin22');

$mongopath="/home/backup/mongo/";
$redispath="/home/backup/redis/";

$datestr=date("Ymd");
if(isset($_REQUEST['date'])){
	$datestr=$_REQUEST['date'];
}
$filesmongo = glob($mongopath . "mongo".$datestr."*.gz");
$filesredis = glob($redispath . "redis".$datestr."*.gz");
$mongobk=$filesmongo[0];
$redisbk=$filesredis[0];

$fh = fopen($mongobk, 'rb');
$fh2 = fopen($redisbk, 'rb');
$uploadName="/mongobackup/".basename($mongobk);
$uploadName2="/redisbackup/".basename($redisbk);
try {
	$rsp = $upyun->writeFile ( $uploadName, $fh, True ); // 上传图片，自动创建目录
	$rsp = $upyun->writeFile ( $uploadName2, $fh2, True ); // 上传图片，自动创建目录
} catch ( Exception $e ) {
	echo $e->getCode ();
	echo $e->getMessage ();
}
// $upyun->delete("/party/$oldname");
// $rsp = $upyun->writeFile("/party/$oldname", $fh, True);   // 上传图片，自动创建目录
fclose($fh);
fclose($fh2);
echo basename($mongobk)." OK";
echo basename($redisbk)." OK";

