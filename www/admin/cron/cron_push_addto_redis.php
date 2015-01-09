<?php
include_once '../../../define.php';
include_once '../model/PushBase.php';
$push=new PushBase();
$ret=$push->insertPushInRedis();
echo "add $ret";