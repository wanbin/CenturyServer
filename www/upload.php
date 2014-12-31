<?php
include '../define.php';
file_put_contents("upyun.log", print_r($_FILES,true),FILE_APPEND);
$file_name=$_FILES['upload_file']['tmp_name'];
include_once FRAMEWORK.'upyun/upyun.class.php';
$upyun = new UpYun('centurywar', 'wan', 'wanbin22');
$fh = fopen($file_name, 'rb');
$newfileName=md5($file_name.time());
$uploadName="/upload/$newfileName.png";
$rsp = $upyun->writeFile($uploadName, $fh, True);   // 上传图片，自动创建目录
fclose($fh);

$ret=array("success"=>true,"msg"=>"失败", "file_path"=>"http://cnd.centurywar.cn/$uploadName");
echo json_encode($ret);