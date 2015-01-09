<?php
//读某个人的信息，把这个人的未读标记去掉
$type=$_REQUEST['type'];
$content=$_REQUEST['content'];
include_once PATH_HANDLER . 'WordsHandler.php';
$words = new WordsHandler($uid);
$arr=explode("\n", $content);
foreach ($arr as $key=>$value){
	if(empty($value)){
		continue;
	}
	$words->newWords($value, $type);
}
// $words->newWords($content, $type) ;
echo "创建成功".count($arr);