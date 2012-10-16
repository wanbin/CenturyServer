<?php
$dbHost = array ();
$dbHost ['host1'] = array ('host' => 'localhost', 'user' => 'root', 'password' => '123456', 'dbname' => array ('centurywar', 'host' ) );

$cacheHost = array ();
$cacheHost ['host1'] = array ('host' => '127.0.0.1:11211' );
$cacheHost ['redis'] = array ('host' => '127.0.0.1:6379' );

$webHost = $_SERVER ["HTTP_HOST"] . '/server/';
$cdnHost = $_SERVER ["HTTP_HOST"] . '/static/';

$database_xmlpath = PATH_ROOT . "../static/database.xml";
$quest_xmlpath = PATH_ROOT . "../static/quest.xml";

$strings_xmlpath = PATH_ROOT . "../static/language/";
$log_path = PATH_ROOT . "../logs/";
$payment_xmlpath = PATH_ROOT . "../static/payment.xml";

$activity_xmlpath = PATH_ROOT . "../static/activity.xml";
?>