<?php
$update=false;
include_once PATH_HANDLER . 'PageHandler.php';
$page = new PageHandler ( $uid );
$ret = $page->getLinkList ();

$pagelist=$page->getPageList(1);

foreach ($ret as $key=>$value){
	$ret[$key]['html']=getSelect('select_'.$value['_id'],$pagelist,$value['link']);	
}




function getSelect($htmlid,$data,$select=''){
	$head="<select  style='width:100px' id='$htmlid'><option value=''>未选择</option>";
	foreach ($data as $key=>$value){
		if($select==$value['_id'])
		{
			$strz.="<option value='".$value['_id']."' selected>".$value['title']."</option>";
		}else{
			$strz.="<option value='".$value['_id']."'>".$value['title']."</option>";
		}
			
	}
	$foot="</select>";
	return $head.$strz.$foot;
}
