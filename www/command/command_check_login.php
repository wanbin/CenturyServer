<?php
//读某个人的信息，把这个人的未读标记去掉
$email=$_REQUEST['email'];
$passowrd=$_REQUEST['password'];
if($email=='wanbinr@gmail.com'&&$passowrd=='wanbin22'){
	$_SESSION['adminname']=$email;
	echo 1;	
}else{
	echo 0;
}
