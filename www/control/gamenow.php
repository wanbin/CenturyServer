<?php 
global $partyGameArr;
$week=date("w",time());
//时间戳的第几周
$weekcount=ceil((time()-86400*3)/86400/7);
$base=2327;
$weekshowindex=$weekcount-$base;
$ret=$partyGameArr[$weekshowindex];
$ret2=$partyGameArr[$weekshowindex+1];
if(!empty($uid)){
	include_once PATH_HANDLER . 'GameHandler.php';
	$game = new GameHandler ($uid);
	$likeInfo=$game->getLikeInfo($weekshowindex);
}


for($i = 0; $i < 100; $i ++) {
 	getMongo ( $i );
}
// exit();

function getMongo($times) {
	$host = BAIDU_MONGO_HOST;
	$port = BAIDU_MONGO_PORT;
	$dbname = BAIDU_MONGO_DBNAME;
	$user = BAIDU_AK;
	$pwd = BAIDU_SK;
	try {
		/* 建立连接后，在进行集合操作前，需要先select使用的数据库，并进行auth */
		$mongoClient = new MongoClient ( "mongodb://{$host}:{$port}" );
		$mongoDB = $mongoClient->selectDB ( $dbname );
		$mongoDB->authenticate ( $user, $pwd );
		
		/* 接下来就可以对该库上的集群进行操作了，具体操作方法请参考php-mongodb官方文档 */
		
		// 集合并不需要预先创建
		$mongoCollection = $mongoDB->selectCollection ( 'test_mongo' );
		
	} catch ( Exception $e ) {
		die ( $e->getMessage () );
	}
}

?>