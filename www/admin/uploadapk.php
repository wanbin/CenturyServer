<?php
include '../../define.php';

$file_name=$_FILES['upload_file']['tmp_name'];
$oldname=$_FILES['upload_file']['name'];
file_put_contents("apk.log", print_r($_FILES,true),FILE_APPEND);

include_once FRAMEWORK.'upyun/upyun.class.php';
$upyun = new UpYun('centurywarfile', 'wan', 'wanbin22');
$fh = fopen($file_name, 'rb');
$newfileName=md5($file_name.time());

$uploadName="/party/undercover.apk";
$upoldName="/party/$oldname";

try {
	$rsp = $upyun->writeFile ( $uploadName, $fh, True ); // 上传图片，自动创建目录
	$rsp = $upyun->writeFile ( $upoldName, $fh, True ); // 上传图片，自动创建目录
} catch ( Exception $e ) {
	echo $e->getCode ();
	echo $e->getMessage ();
}
// $upyun->delete("/party/$oldname");
// $rsp = $upyun->writeFile("/party/$oldname", $fh, True);   // 上传图片，自动创建目录
fclose($fh);
$msg= "upload $oldname success!";

Header("Location:index.php?showpage=version_edit&msg=".$msg);
return;