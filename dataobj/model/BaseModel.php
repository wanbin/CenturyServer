<?php
require_once FRAMEWORK . 'exception/ExceptionConstants.php';
require_once 'MemcacheConstants.php';
include_once 'DBModel.php';
class BaseModel  extends DBModel {
	
	public $gameuid = NULL;
	protected $uid     = NULL;
	protected $server  = NULL;
	protected $useMemcache = null;
	protected $model = null;
	protected $commandAnalysis = null;
	protected $channel="ANDROID";
	protected $itemMC = null;

	
	public function __construct($uid,$channel='ANDROID') {
		parent::__construct();
		$this->debug('firstuid',$uid);
		// 加载config
		$this->channel=$channel;
		if (isset ( $uid )&&!empty($uid)) {
			$this->uid = $uid;
			$this->gameuid = $this->getGameuid($uid);
		}
	}
	
	
	public function getGameuid($uid){
		$this->debug("initUid",$uid);
		$gameuid=$this->redis->HGET("REDIS_USER_GAMEUID",$uid);
		
		if($gameuid>0){
			return $gameuid;
		}
		if (strlen ( $uid ) == strlen ( "5A74E27E8AC44C778731B7A8A8207250" )) {
			$this->channel = 'IOS';
		} elseif (substr ( $uid, 1, 5 ) == substr ( "ouHjQjpu175ug-jVh0Wdw5i--Xgw", 1, 5 )) {
			$this->channel = 'WX';
		}elseif(substr ( $uid, 1, 5 )=='201'){
			$this->channel = 'WEB';
		}
		
		$userinfo = array (
				'uid' => $uid,
				'channel' => $this->channel 
		);
		$gameuid = $this->insertMongo ( $userinfo, 'users' );
		
		$this->debug ( 'user_gameuid', array (
				$uid => $gameuid 
		) );
		
		$this->redis->HMSET ( "REDIS_USER_GAMEUID", array (
				$uid => $gameuid 
		) );
		return $gameuid;
	}
	
	
	public function getUserInfo($uid){
		return $this->getOneFromMongo(array('uid'=>$uid), 'users');
	}	
	
	
	
		// =================================MYSQL============================================//
		

}
