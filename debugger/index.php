<?php
include_once '../define.php';
// echo PATH_DEBUGGER;
$fso  = opendir(PATH_DEBUGGER);
// echo 'dd';
// exit();
$exceptArr=array("index.php",'config.inc.php');
while($flist=readdir($fso)){
	if($flist != '.' && $flist != '..'&& !in_array($flist, $exceptArr)&&is_file($flist)){
		$output[] = $flist;
	}
}


foreach ($output as $url){
	echo "<div><a href='$url' target='_blank'> $url</a></div>";
}
// print_R($output);