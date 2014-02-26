<?php


include('config.inc.php');
include('db.class.php');

$ret = $_REQUEST['push_result'];

//print_r($_REQUEST);
$ret = stripslashes($ret);

$res = json_decode($ret,true);


$result = array();

$res['app_keys'] = "d0085b49b0682dbbb3a36ff5";
 if ($res['app_keys'] == appkeys ){
	dataConnect();
	$sql = "UPDATE `".DB_TAB."` SET `total_user` = '".$res['total_user']."', `send_cnt` = '".$res['send_cnt']."' WHERE `sendno` = ".$res['sendno']."";
	echo "$sql";
	$query = mysql_query($sql);
 }

record($ret);

function record($con)
{
	$con .= '  '.date('Y-m-d H:i:s',time())."\r\n";
	$handler = fopen('log.txt','a');
	fwrite($handler,$con);
	fclose($handler);
 
}
 
?>