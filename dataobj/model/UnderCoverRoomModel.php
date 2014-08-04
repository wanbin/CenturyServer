<?php
/**
 * @author WanBin @date 2013-08-03
 * 谁是卧底房间信息
 */
require_once PATH_MODEL . 'BaseModel.php';
class UnderCoverRoomModel extends BaseModel {
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
	public function initRoom($peoplecount) {
		$tablename = 'wx_undercover_room';
		$endtime = time () - 3600;
		$newroom = false;
		$content = array ();
		$ret = $this->oneSqlSignle ( "select id from $tablename where id>=1000 and time<$endtime limit 1" );
		$ret=$ret[0];
		if (empty ( $ret )) {
			$maxroom = $this->oneSqlSignle ( "select max(id) roomid from wx_undercover_room" );
			$roomid = max ( 1000, $maxroom ['roomid'] + 1 );
			$newroom = true;
		} else {
			$roomid = $ret ['id'];
		}
		$time = time ();
		$temContent = $this->initcontent ( $peoplecount );
		$conjson = '';
		foreach ( $temContent ['content'] as $key => $value ) {
			$conjson .= $value . '_';
		}
		$gameuid = $this->gameuid;
		if ($newroom == true) {
			$this->oneSql ( "insert into $tablename values($roomid,$gameuid,$time,$peoplecount,'$conjson',0,'')" );
		} else {
			$this->oneSql ( "update $tablename set gameuid=$gameuid,time=$time,peoplecount=$peoplecount,content='$conjson',users='[]',nowcount=0 where id=$roomid" );
		}
		$father = $temContent ['father'];
		$son = $temContent ['son'];
		$sonIndex = $temContent ['sonindex'];
		$str = "创建房间 【 $roomid 】 成功 \n 本房间人数：$peoplecount \n 平民：$father \n 卧底：$son \n 卧底编号： $sonIndex";
		if ($this->echoit) {
			echo $str;
		}
		return $str;
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
	public function getInfo($roomid) {
		$tablename = 'wx_undercover_room';
		$sql = "select * from $tablename where id=$roomid ";
		$ret = $this->oneSqlSignle ( $sql );
		if ($ret ['gameuid'] == $this->gameuid) {
			$nowcount = $ret ['nowcount'];
			$pcount = $ret ['peoplecount'];
			$str = "您创建了本房间：\n 当前人数:$nowcount \n总人数:$pcount \n";
			if ($this->echoit) {
				echo $str;
			}
			return $str;
		}
		$userArr = json_decode ( $ret ['users'], true );
		if (! isset ( $userArr ))
			$userArr = array ();
		$contentStr = $ret ['content'];
		$contentArr = explode ( '_', $contentStr );
		foreach ( $userArr as $key => $value ) {
			if ($value ['uid'] == $this->gameuid) {
				$str = $contentArr [$key];
				$id=$key+1;
				$str = "您的身份为：$str\n您的编号为：$id";
				if ($this->echoit) {
					echo $str;
				}
				return $str;
			}
		}
		if ($ret ['peoplecount'] == $ret ['nowcount']) {
			$str = "此房间已满或已经超时，召集自己的好友回复1重新开一局吧~~";
			if ($this->echoit) {
				echo $str;
			}
			return $str;
		}
		$nowindex = count ( $userArr );
		$userArr [] = array (
				'uid' => $this->gameuid,
				'time' => time ()
		);
		$str = $contentArr [$nowindex];
		$id = $nowindex + 1;
		$userstr = json_encode ( $userArr );
		$this->oneSql ( "update $tablename set users='$userstr',nowcount=nowcount+1 where id=$roomid" );
		$str = "您的身份为：$str\n您的编号为：$id";
		if ($this->echoit) {
			echo $str;
		}
		return $str;
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
				$result[$policeindex]='杀手';
				$police--;
			}
		}
		return array (
				'content' => $result,
				'killer' => $killer,
				'police' => $police
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