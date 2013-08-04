<?php
/**
 * @author WanBin @date 2013-08-03
 * 谁是卧底房间信息
 */
require_once PATH_DATAOBJ.'BaseModel.php';
class UnderCoverRoomModel extends BaseModel {
	public $echoit = false;
	public function getEnableRoom() {
		$endtime = time () - 3600;
		$ret = $this->oneSql ( "select id from wx_undercover_room where id>1000 and time<$endtime limit 1" );
		if (empty ( $ret )) {
			$maxroom = $this->oneSql ( "select max(id) roomid from wx_undercover_room" );
			return max ( 1000, $maxroom [0] ['roomid'] );
		} else {
			return $ret ['id'];
		}
	}
	public function initRoom($peoplecount, $gameuid = 0) {
		$tablename = 'wx_undercover_room';
		$endtime = time () - 3600;
		$newroom = false;
		$content = array ();
		$ret = $this->oneSql ( "select id from wx_undercover_room where id>=1000 and time<$endtime limit 1" );
		if (empty ( $ret )) {
			$maxroom = $this->oneSql ( "select max(id) roomid from wx_undercover_room" );
			$roomid = max ( 1000, $maxroom ['roomid'] + 1 );
			$newroom = true;
		} else {
			$roomid = $ret ['id'];
		}
		$time = time ();
		$temContent = $this->initcontent ( $peoplecount );
		$conjson='';
		foreach ($temContent['content'] as $key=>$value)
		{
			$conjson.=$value.'_';
		}
		if ($newroom == true) {
			$this->oneSql ( "insert into $tablename values($roomid,$gameuid,$time,$peoplecount,'$conjson',0,'')" );
		} else {
			$this->oneSql ( "update $tablename set gameuid=$gameuid,time=$time,peoplecount=$peoplecount,content='$conjson',users='[]',nowcount=0 where id=$roomid" );
		}
		$father=$temContent['father'];
		$son=$temContent['son'];
		$sonIndex=$temContent['sonindex'];
		$str = "创建房间 【 $roomid 】 成功 \n 本房间人数：$peoplecount \n 平民:$father \n 卧底：$son \n 卧底编号： $sonIndex";
		if ($this->echoit) {
			echo $str;
		}
		return $str;
	}
	public function getChengfa(){
		global $chengfa;
		$getArr = array ();
		$returnArr = array ();
		$returnStr="接受惩罚吧：\n";
		for($i = 0; $i < 6; $i ++) {
			do {
				$rand = rand ( 1, count ( $chengfa ) );
			} while ( in_array ( $rand, $getArr ) );
			$getArr [] = $rand;
			$id=$i+1;
			$returnStr.= "$id.".$chengfa [$rand - 1]."\n";
		}
		return $returnStr;
	}
	
	
	public function getInfo($roomid,$gameuid=0)
	{
		$tablename = 'wx_undercover_room';
		$sql="select * from $tablename where id=$roomid ";
		$ret=$this->oneSql($sql);
		if($ret['gameuid']==$gameuid)
		{
			$nowcount = $ret ['nowcount'];
			$pcount = $ret ['peoplecount'];
			$str = "您创建了本房间：\n 当前人数:$nowcount \n总人数:$pcount \n";
			if ($this->echoit) {
				echo $str;
			}
			return $str;
		}
		$userArr= json_decode($ret['users'],true);
		if(!isset($userArr))
			$userArr=array();
		$contentStr =  $ret ['content'];
		$contentArr=explode('_', $contentStr);
		foreach ( $userArr as $key => $value ) {
			if ($value ['uid'] == $gameuid) {
				$str=$contentArr [$key];
				$str = "您的身份为：$str\n 您的编号为：$key";
				if ($this->echoit) {
					echo $str;
				}
				return $str;
			}
		}
		if ($ret ['peoplecount'] == $ret ['nowcount']) {
			$str = "房间已经满了";
			if ($this->echoit) {
				echo $str;
			}
			return $str;
		}
		$nowindex = count ( $userArr );
		$userArr [] = array (
				'uid' => $gameuid,
				'time' => time ()
		);
		$id=$nowindex + 1;
		$str = $contentArr [$id];
		$userstr = json_encode ( $userArr );
		$this->oneSql ( "update $tablename set users='$userstr',nowcount=nowcount+1 where id=$roomid" );
		$str = "您的身份为：$str\n 您的编号为：$id";
		if ($this->echoit) {
			echo $str;
		}
		return $str;
	}
	public  function hexDecode($s) {
    return preg_replace('/(\w{2})/e',"chr(hexdec('\\1'))",$s);
}
	
	public function initcontent($peoplecount){
		global $word;
		$ramdom = $word [array_rand ( $word )];
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
		$sonIndex='';
		$sonArr=array();
		for($n = 0; $n < $soncount; $n ++) {
			do {
				$tem = rand ( 0, $peoplecount - 1 );
			} while ( $arraycontent [$tem] == $son );
			$arraycontent [$tem] = $son;
			$sonArr[$tem + 1]=$tem + 1;
		}
		
		sort($sonArr);
		foreach ( $sonArr as $key => $value ) {
			$sonIndex .= "$value 号 ";
		}
		return array('content'=>$arraycontent,'father'=>$father,'son'=>$son,'soncount'=>$soncount,'sonindex'=>$sonIndex);
		
	}
	
	
	/**
	 * 得到所有记录
	 */
	protected function get() {
		$where = array ('gameuid' => $this->gameuid );
		$res = $this->hsSelectAll ( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
		$ret = array ();
		foreach ( $res as $key => $value ) {
			foreach ( $value as $jsonKey => $jsonValue )
				if (in_array ( $jsonKey, array ('content' ) )) {
					$value [$jsonKey] = json_decode ( $jsonValue, true );
				}
			$temid = $value ['templateid'];
			unset ( $value ['templateid'] );
			$ret [$value ['templateid']] = $value;
		}
		return $ret;
	}
	/**
	 * 得到一条记录
	 *
	 * @param $id unknown_type
	 * @return Ambigous <boolean, multitype:, multitype:multitype: >
	 */
	protected function getOne() {
		$where = array ('gameuid' => $this->gameuid );
		$res = $this->hsSelectAll ( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
		return $res;
	}
	
	protected function getOneSingle($templateid) {
		$where = array ( 'id' => $id );
		$res = $this->hsSelectOne ( $this->getTableName (), $this->gameuid, $this->getFields (), $where );
		return $res;
	}
	/**
	 * 更新信息
	 *
	 * @param $content unknown_type
	 * @return Ambigous <boolean, number, multitype:>
	 */
	protected function update($content) {
		$where = array ('gameuid' => $this->gameuid );
		$res = $this->hsUpdate ( $this->getTableName (), $this->gameuid, $content, $where );
		return $res;
	}
	
	protected function updateOne( $content,$id) {
		$where = array ( 'id' => $id);
		$res = $this->hsUpdate ( $this->getTableName (), $this->gameuid, $content, $where );
		return $res;
	}
	
		/**
	 * 添加一条信息
	 *
	 * @param $content unknown_type
	 * @return Ambigous <boolean, number, multitype:>
	 */
	protected function add($content) {
		$fields = explode ( ',', $this->getFields () );
		$insert ['gameuid'] = $this->gameuid;
		foreach ( $content as $key => $value ) {
			if (in_array ( $key, array ('content') )) {
				$value = json_encode ( $value );
			}
			if (in_array ( $key, $fields )) {
				$insert [$key] = $value;
			}
		}
		return $this->hsInsert ( $this->getTableName (), $this->gameuid, $insert );
	}
	
	protected function addarr($content) {
		foreach ( $content as $key => $value ) {
			foreach ( $value as $jsonKey => $jsonValue ) {
				if (in_array ( $jsonKey, array ('content' ) )) {
					$content [$key] [$jsonKey] = json_encode ( $jsonValue );
				}
			}
		}
		return $this->hsMultiInsert ( $this->getTableName (), $this->gameuid, $content );
	}
	
	protected function init($id ) {
		$insert = array ( 'id' => $id);
		return $this->hsInsert ( $this->getTableName (), $this->gameuid, $insert );
	}
	/**
	 * 删除一条信息
	 *
	 * @return number
	 */
	protected function del() {
		$where = array ('gameuid' => $this->gameuid );
		return $this->hsDelete ( $this->getTableName (), $this->gameuid, $where );
	}
	
	protected function delOne( $id ) {
		$where = array ( 'id' => $id);
		return $this->hsDelete ( $this->getTableName (), $this->gameuid, $where );
	}
	
	protected function getFields() {
		return 'id,gameuid,time,peoplecount,content,nowcount';
	}
	
	protected function getTableName() {
		return "wx_undercover_room";
	}
}