<?php
include_once 'model/PushBase.php';
$push=new PushBase();
$ret=$push->getResect(100);
foreach ($ret as $key=>$value){
	$ret[$key]['sendtime']=date("Y-m-d H:i:s",$value['sendtime']);
	$ret[$key]['remain']=$push->getRemain($value['_id']);
}

