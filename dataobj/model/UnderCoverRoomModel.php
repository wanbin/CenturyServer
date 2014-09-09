<?php
/**
 * @author WanBin @date 2013-08-03
 * 谁是卧底房间信息
 */
require_once PATH_MODEL . 'BaseModel.php';
class UnderCoverRoomModel extends BaseModel {
	
	protected function saveRoom($roominfo){
		return $this->insertMongo($roominfo, 'room_save');
	}
	
	public $echoit = false;
	public function getEnableRoom() {
		$endtime = time () - 3600;
		$ret = $this->oneSqlSignle ( "select id from wx_undercover_room where id>1000 and time<$endtime limit 1" );
		if (empty ( $ret )) {
			$maxroom = $this->oneSqlSignle ( "select max(id) roomid from wx_undercover_room" );
			return max ( 1000, $maxroom ['roomid'] );
		} else {
			return $ret ['id'];
		}
	}
	
	public function getRepentCount($msg){
			
	}
	
	public function getChengfa($type) {
		global $chengfa;
		global $chengfaonline;
		$getArr = array ();
		$returnArr = array ();
		$stradd = "(本地版)";
		if ($type == 2) {
			$stradd = "(网络版)";
			$chengfa = $chengfaonline;
		}
		$returnStr = "真心话大冒险 $stradd :\n【请输的同学摇骰子选择】\n";
		for($i = 0; $i < 6; $i ++) {
			do {
				$rand = rand ( 1, count ( $chengfa ) );
			} while ( in_array ( $rand, $getArr ) );
			$getArr [] = $rand;
			$id = $i + 1;
			$returnStr .= "$id." . $chengfa [$rand - 1] . "\n";
		}
		$returnStr.="\n";
		return $returnStr;
	}
	
	public function hexDecode($s) {
		return preg_replace ( '/(\w{2})/e', "chr(hexdec('\\1'))", $s );
	}
	
	public function initKiller($peoplecount){
		if($peoplecount<6)
		{
// 			return false;
		}
		$killer=floor( $peoplecount/4);
		$police=$killer;
		$policeold=$killerold=$killer;
		$result=array();
		for($i=0;$i<$peoplecount;$i++){
			$result[$i]="平民";
		}
		
		$faguanrate=rand(0, $peoplecount-1);
		$result[$faguanrate]='法官';
		
		while ($killer>0){
			$killerindex=rand(0, $peoplecount-1);
			if($result[$killerindex]=='平民')
			{
				$result[$killerindex]='杀手';
				$killer--;
			}
		}
		
		while ($police>0){
			$policeindex=rand(0, $peoplecount-1);
			if($result[$policeindex]=='平民')
			{
				$result[$policeindex]='警察';
				$police--;
			}
		}
		return array (
				'content' => $result,
				'killer' => $killerold,
				'police' => $policeold
		);
	}
	
	public function initcontent($peoplecount) {
		global $word;
		$ramdom = $word [array_rand ( $word )];
// 		$ramdom='wan_bin_ddd';
		$tem = explode ( '_', $ramdom );
		$ramdomfather = rand ( 1, 2 );
		if ($ramdomfather == 1) {
			$father = $tem [0];
			$son = $tem [1];
		} else {
			$father = $tem [1];
			$son = $tem [0];
		}
		$soncount = max ( floor ( $peoplecount / 4 ), 1 );
		$arraycontent = array_fill ( 0, $peoplecount, $father );
		$sonIndex = '';
		$sonArr = array ();
		for($n = 0; $n < $soncount; $n ++) {
			do {
				$tem = rand ( 0, $peoplecount - 1 );
			} while ( $arraycontent [$tem] == $son );
			$arraycontent [$tem] = $son;
			$sonArr [$tem + 1] = $tem + 1;
		}
		
		sort ( $sonArr );
		foreach ( $sonArr as $key => $value ) {
			$sonIndex .= "$value 号 ";
		}
		return array (
				'content' => $arraycontent,
				'father' => $father,
				'son' => $son,
				'soncount' => $soncount,
				'sonindex' => $sonIndex
		);
	}
}