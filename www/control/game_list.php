<?php 

include_once PATH_HANDLER . 'GameHandler.php';
$game = new GameHandler ($uid,'WEB');
$ret=$game->getGameList(1);
foreach ($ret as $key=>$value){
	$ret[$key]['showtime']=date("Y-m-d",$value['showtime']);
}

?>
