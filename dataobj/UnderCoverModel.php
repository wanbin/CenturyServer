<?php
/**
 * @author WanBin @date 2013-08-03
 * 微信用户表
 */
require_once PATH_DATAOBJ.'BaseModel.php';
class UnderCoverModel extends BaseModel {
	public function __construct( $uid = null) {
		parent::__construct($uid);
		if (empty ( $this->gameuid )) {
			$this->add ( array (
					'uid' => $uid,
					'regtime' => time ()
			) );
			parent::__construct($uid);
		}
	}
	
	
	public function Log($content) {
		$this->hsInsert ( $this->getTableNameLog (), array (
				'gameuid'=>$this->gameuid,
				'content' => $content,
				'time' => time ()
		) );
	}
	
	/**
	 * 返回信息类
	 *
	 * @param unknown_type $keyword
	 * @param unknown_type $uid
	 * @return string
	 */
	public function returncontent($keyword) {
		$this->Log ( $keyword );
		$helpStr = $this->getSampleHelpStr ();
		$type = $keyword ;
		if ($type == "帮助" || $type == "【帮助】" || $type == "help") {
			return $this->getHelpStr ();
		}
		if ($type == "规则" || $type == "【规则】" || $type == "rule") {
			return $this->getRuleStr ();
		}
		$type=intval($keyword);
		if ($type > 3 && $type <= 15) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ( $this->uid );
			$str = $UnderRoomCache->initRoom ( $type );
			return $str . $helpStr;
		} else if ($type == 1) {
			$str = "谁是卧底游戏创建成功，您为法官，请输入参与人数（不包括法官 4-15人）：";
			return $str.$helpStr;
		} else if ($type == 2) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ($this->uid);
			$str = $UnderRoomCache->getChengfa ( 2 );
			return $str.$helpStr;
		} else if ($type == 3) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ($this->uid);
			$str = $UnderRoomCache->getChengfa ( 3);
			return $str.$helpStr;
		}  else if ($type >= 1000) {
			$gameuid = $UnderCache->gameuid;
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ($this->uid);
			$str = $UnderRoomCache->getInfo ( $type );
			return $str.$helpStr;
		} else {
			return $this->getHelpStr();
		}
	}
	protected function getHelpStr() {
		return "欢迎使用谁是卧底游戏助手，回复以下内容快速开始：\n 1.创建谁是卧底游戏\n 2.真心话大冒险（网络版)\n 3.真心话大冒险（本地版）\n 4-15.创建谁是卧底房间\n 1000-9999.进入相应的房间";
	}
	//返回制作团队
	protected function getEmail() {
		return "谁是卧底请您选择项目：\n 4-14.创建谁是卧底游戏: \n 输入 20 返回真心话大冒险：";
	}
	protected function getSampleHelpStr() {
		return "\n\n回复【帮助】显示帮助内容 \n回复【规则】显示游戏规则";
	}
	protected function getRuleStr() {
		return "\n【谁是卧底】游戏规则 \n
				人数：法官1人，玩家4-15人\n
				开局：法宫向我发送4-15位参与人数，我会告诉你平民及卧底身份及编号，以及我为你们创建的房间号\n
				参与：把房间号通过群或其它方式告知参与者，参与者向我发送房间号，我会给他们发送身份及说明编号\n
				进行：法官组织每位玩家依次发言，每位玩家简短的描述自己的身份以及自己的编号\n
				投票：描述一轮结束后，玩家投票选择卧底，票数较多的玩家身亡，如果票数相等，则相等的玩家再次描述，再次投票。分出结果后，法官公布结果（冤死或卧底）\n
				胜利：卧底全被揪出，则平民胜利，卧底数大于等于平民数，卧底胜利\n
				惩罚：如果在网上玩，则向我发送2，我会返回随机6个真心话及大冒险，让玩家投骰子选择；如果在线下玩，则发送3，我会给你一个适于线下的真心话大冒险\n";
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
				if (in_array ( $jsonKey, array ( ) )) {
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
		$where = array ( 'gameuid' => $this->gameuid );
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
	
	protected function updateOne( $content,$gameuid) {
		$where = array ( 'gameuid' => $this->gameuid);
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
		foreach ( $content as $key => $value ) {
			if (in_array ( $key, array () )) {
				$value = json_encode ( $value );
			}
			if (in_array ( $key, $fields )) {
				$insert [$key] = $value;
			}
		}
		return $this->hsInsert ( $this->getTableName (),$insert );
	}
	
	protected function addarr($content) {
		foreach ( $content as $key => $value ) {
			foreach ( $value as $jsonKey => $jsonValue ) {
				if (in_array ( $jsonKey, array ( ) )) {
					$content [$key] [$jsonKey] = json_encode ( $jsonValue );
				}
			}
		}
		return $this->hsMultiInsert ( $this->getTableName (), $this->gameuid, $content );
	}
	
	protected function init($gameuid ) {
		$insert = array ( 'gameuid' => $this->gameuid);
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
	
	protected function delOne( $gameuid ) {
		$where = array ( 'gameuid' => $this->gameuid);
		return $this->hsDelete ( $this->getTableName (), $this->gameuid, $where );
	}
	
	protected function getFields() {
		return 'gameuid,uid,regtime';
	}
	
	protected function getTableName() {
		return "wx_account";
	}
	protected function getTableNameLog() {
		return "wx_log";
	}
}