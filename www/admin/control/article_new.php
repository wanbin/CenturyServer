<?php
$def_date=date("Y-m-d H:i:s",time()+60);
$id=$_REQUEST['id'];
$update=false;
if($id>0){
	include_once PATH_HANDLER.'ArticleHandler.php';
	$game=new ArticleHandler($uid);
	$ret=$game->getOne($id);
	$def_date=date("Y-m-d H:i:s",$ret['showtime']);
	$update=true;
}

$typearray = array (
		array (
				"_id" => 1,
				'title' => "最新游戏" 
		) ,
		array (
				"_id" => 2,
				'title' => "游戏帮助"
		) 
);
$typeselect = getSelect ( "typeselect", $typearray, $ret ['type'] );
function getSelect($htmlid, $data, $select = '') {
	$head = "<select  style='width:100px' id='$htmlid'><option value=''>未选择</option>";
	foreach ( $data as $key => $value ) {
		if ($select == $value ['_id']) {
			$strz .= "<option value='" . $value ['_id'] . "' selected>" . $value ['title'] . "</option>";
		} else {
			$strz .= "<option value='" . $value ['_id'] . "'>" . $value ['title'] . "</option>";
		}
	}
	$foot = "</select>";
	return $head . $strz . $foot;
}
