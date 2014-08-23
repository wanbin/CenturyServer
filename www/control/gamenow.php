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


$host=BAIDU_MONGO_HOST;
$port=BAIDU_MONGO_PORT;
$dbname=BAIDU_MONGO_DBNAME;
$user = BAIDU_AK;
$pwd = BAIDU_SK;


try {
	/*建立连接后，在进行集合操作前，需要先select使用的数据库，并进行auth*/
	$mongoClient = new MongoClient("mongodb://{$host}:{$port}");
	$mongoDB = $mongoClient->selectDB($dbname);
	$mongoDB->authenticate($user, $pwd);

	/*接下来就可以对该库上的集群进行操作了，具体操作方法请参考php-mongodb官方文档*/

	//集合并不需要预先创建
	$mongoCollection = $mongoDB->selectCollection('test_mongo');

	//插入数据
	$array = array(
			'no' => new MongoInt32('2007'),
			'name' => 'this is a test message',
	);
	$mongoCollection->insert($array);
	$array = array(
			'no' => new MongoInt32('2008'),
			'name' => 'this is another test message',
	);
	$mongoCollection->insert($array);
	$array = array(
			'no' => new MongoInt32('2009'),
			'name' => 'xxxxxxxx',
	);
	$mongoCollection->insert($array);

	//删除数据
	$mongoCollection->remove(array('no'=> 2008));

	//更新数据
	$mongoCollection->update(array('no' => 2009), array('$set'=>array('name'=>'yyyyyy')));

	//检索数据
	$mongoCursor = $mongoCollection->find();
	while($mongoCursor->hasNext()) {
		$ret = $mongoCursor->getNext();
		echo json_encode($ret) . '<br />';
	}

	//删除集合
	$mongoCollection->drop();

} catch (Exception $e) {
	die($e->getMessage());
}

?>