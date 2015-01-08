<?php
include_once FRAMEWORK.'upyun/upyun.class.php';
$upyun = new UpYun ( 'centurywarfile', 'wan', 'wanbin22' );
$ret = $upyun->getList ( '/party/' );
foreach ($ret as $key=>$value){
	$ret[$key]['time']=date("Y-m-d H:i:s",$value['time']);
	$ret[$key]['size']=sizecount($value['size']);
}
function sizecount($filesize) {
	if($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 . ' gb';
	} elseif($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 . ' mb';
	} elseif($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' kb';
	} else {
		$filesize = $filesize . ' bytes';
	}
	return $filesize;
}