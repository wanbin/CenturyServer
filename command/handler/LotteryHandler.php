<?php
require_once PATH_CACHE . 'LotteryCache.php';
class LotteryHandler extends LotteryCache {
	public function updateSetting($content) {
		$lotteryinfo = $this->getSetting ( $this->gameuid );
		$lotteryarr = $lotteryinfo ['content'];
		$hasLotteryIdArr=array();
		foreach ( $lotteryarr as $key => $value ) {
			$hasLotteryIdArr[$value['id']]=$value['haslottery'];
		}
		//这个其实是客户端的一个BUG，更新信息的时候，已经抽奖不能变
		foreach ($content as $key=>$value){
			$content[$key]['haslottery']=$hasLotteryIdArr[$value['id']];
		}
		
		parent::updateSetting ( $content );
	}
	public function getSetting($roomid = 0) {
		return parent::getSetting ( $roomid );
	}
	public function shake($roomid) {
		$ret = parent::shake ( $roomid );
		return $ret;
	}
	public function updateShake($isshake) {
		$ret=parent::updateShake ( $isshake );
		if ($isshake) {
			include_once PATH_HANDLER . '/RoomsHandler.php';
			$roomhl = new RoomsHandler ( $this->uid );
			
			include_once PATH_HANDLER . 'AccountHandler.php';
			$account = new AccountHandler ( $this->uid );
			
			$gameuidList = $roomhl->getRoomUserList ( $this->gameuid );
			$gameuidList = array_flip ( $gameuidList );
			// 把已经抽过奖的用户排除
			foreach ( $this->getHasLottery () as $key => $value ) {
				unset ( $gameuidList [$key] );
			}
			$content = "抽奖开启了互动模式，疯狂的点击吧！";
			foreach ( $gameuidList as $gameuid => $value ) {
				if($gameuid!=$this->gameuid){
					$roomhl->setUserContent($gameuid,$content);
					$account->sendPushByGameuid($gameuid, $content);
				}
			}
		}
		return $ret;
	}
	public function isRoomShake() {
		return parent::isShake ( $this->gameuid );
	}
	public function getUserShackCount($userarr) {
		return parent::getUserShackCount ( $userarr );
	}
	public function LotteryDo($morepeople) {
		// 1.把所有用户的gameuid及互动次数取到
		include_once PATH_HANDLER . '/RoomsHandler.php';
		$roomhl = new RoomsHandler ( $this->uid );
		$gameuidList = $roomhl->getRoomUserList ( $this->gameuid );
		$gameuidList = array_flip ( $gameuidList );
		// 把已经抽过奖的用户排除
		foreach ( $this->getHasLottery () as $key => $value ) {
			unset ( $gameuidList [$key] );
		}
		if (empty ( $gameuidList )) {
			return array (
					'userarr' => array (),
					'lotterycontent' => array('end'=>true)
			);
		}
		
		$gameuidList = array_flip ( $gameuidList );
		$retShackCount = $this->getUserShackCount ( $gameuidList );
		$userList = array ();
		foreach ( $gameuidList as $key => $value ) {
			$userList [$value] = $retShackCount [$value]+10;
		}
		
		// 2.计算要抽取的人数及奖项
		$lotteryinfo = $this->getSetting ( $this->gameuid );
		$lotteryarr = $lotteryinfo ['content'];
		$name = "";
		$gift = "";
		$people = 0;
		$haslottery = 0;
		$lotterycontentIndex = 0;
		$temLotteryResult = array ();
		foreach ( $lotteryarr as $key => $value ) {
			$name = $value ['name'];
			$gift = $value ['gift'];
			$people = $value ['people'];
			$haslottery = $value ['haslottery'];
			$lotterycontentIndex = $key;
			if ($people > $haslottery) {
				$onescount = 1;
				if ($morepeople) {
					$onescount = ceil ( $people / 5 );
					$onescount = min ( array (
							$onescount,
							$people - $haslottery 
					) );
				}
				$temLotteryResult = $this->getLotteryResult ( $userList, $onescount, $this->isShake ( $this->gameuid ) );
				break;
			}
		}
		if (empty ( $gameuidList )) {
			return array (
					'userarr' => array (),
					'lotterycontent' => array('end'=>true)
			);
		}
		
		// 3.抽取，标记，推送，组织，返回
		$lotteryarr [$lotterycontentIndex] ['haslottery'] += count ( $temLotteryResult );
		$this->updateSetting ( $lotteryarr );
		
		
		include_once PATH_HANDLER . 'AccountHandler.php';
		$account = new AccountHandler ( $this->uid );
		
		foreach ( $temLotteryResult as $value => $gameuid ) {
			//这里进行发推送
			$content = "恭喜抽中【".$lotteryarr [$lotterycontentIndex] ['name'] ."】".$lotteryarr [$lotterycontentIndex] ['gift'];
			
			$roomhl->setUserContent($gameuid,$content);
			$account->sendPushByGameuid($gameuid, $content);
			
			$this->setHasLottery ( $gameuid, $lotteryarr [$lotterycontentIndex] ['id'] );
		}
		
		return array (
				'userarr' => $temLotteryResult,
				'lotterycontent' => $lotteryarr [$lotterycontentIndex] 
		);
	}
	protected function setHasLottery($gameuid,$giftid) {
		$rediska = new Rediska ();
		$key = "Lottery_has_lottery_" . $this->gameuid;
		$hash = new Rediska_Key_Hash ( $key );
		$hash->set ( $gameuid, $giftid );
		return $hash->count ();
	}
	
	public function getHasLottery() {
		$rediska = new Rediska ();
		$key = "Lottery_has_lottery_" . $this->gameuid;
		$hash = new Rediska_Key_Hash ( $key );
		return $hash->getFieldsAndValues ();
	}
	public function getLotteryResult($gameuidarr, $onescount, $isShake) {
		$result = array ();
		for(; $onescount > 0; $onescount --) {
			$temgameuid = 0;
			if ($isShake) {
				// 这一块加10是让所有人都有机会抽到奖品
				$maxShack = array_sum ( $gameuidarr );
				$temrand = rand ( 1, $maxShack );
				foreach ( $gameuidarr as $key => $value ) {
					$temgameuid = $key;
					$temrand -= $value;
					if ($temrand <= 0) {
						break;
					}
				}
			} else {
				$temgameuid = array_rand ( $gameuidarr );
			}
			$result [] = $temgameuid;
			unset ( $gameuidarr [$temgameuid] );
		}
		return $result;
	}
	public function LotteryReset() {
		$rediska = new Rediska ();
		$key = "Lottery_has_lottery_" . $this->gameuid;
		$hash = new Rediska_Key_Hash ( $key );
		$hash->delete ();
		
		$keyCount = "Lottery_Redis_Shake_Count_" . $this->gameuid;
		$hashClick = new Rediska_Key_Hash ( $keyCount );
		$hashClick->delete ();
		
		$keyList = "Lottery_Redis_Shake_List_" . $this->gameuid;
		$list = new Rediska_Key_List ( $keyList );
		$list->delete ();
		
		$lotteryinfo = $this->getSetting ( $this->gameuid );
		$lotteryarr = $lotteryinfo ['content'];
		foreach ( $lotteryarr as $key => $value ) {
			$lotteryarr [$key] ['haslottery'] = 0;
		}
		
		$this->updateSetting ( $lotteryarr );
		return true;
	}
}