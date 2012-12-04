<?php
//
// 命令对象基类
//
class BaseCommand {
	protected $uid; // 用户id
	protected $gameuid;
	protected $user_account;
	protected $itemObject; // 配置文件
	protected $buildingObject; // 建筑信息（铁矿，，，）
	protected $soldierObject;
	protected $runTimesObject; // 暂时不用
	protected $accountObject;
	protected $dungeonObject; // 地下城
	protected $marchObject;
	protected $heroObject;
	protected $sns_id;
	protected $mercenaryobject;
	protected $session_key;
	protected $server;
	protected $issync = FALSE;
	protected $commandName = '';
	private $noValidateCommand;
	private $noServerCommand;
	protected $qqPayObject=null;
	protected $current_command = '';
	protected $isSyncPoints = 0;
	protected $isQQPoints = 1;
	protected $cacheItem = array();
	protected $version = '0.0.0';
	protected $questrStr = 'quest';
	public function setSyncPoints($bool = false) {
		$this->isSyncPoints = $bool;
		//如果设置了这个属性，强制不进行同步
		
	}
	public function execute($command, $param, $sign_arr) {
		$errorCode = 0;
		$this->noValidateCommand = $GLOBALS ['config'] ['noValidateCommand'];
		
		$this->noServerCommand = $GLOBALS ['config'] ['noServerCommand'];
		
		if (isset ( $GLOBALS ['config'] ['syncQQpay'] )) {
			$this->isQQPoints = $GLOBALS ['config'] ['syncQQpay'];
		}
		
		try {
			
			$this->server = $sign_arr ['server'];
			if (! in_array ( $command, $this->noServerCommand )) {
				if (! isset ( $this->server )) {
					$this->licitException ( 'server is empty', 1201 );
				}
			}
			if (! in_array ( $command, $this->noValidateCommand )) {
				// 验证account
				$accountMC = $this->createAccountModel ( $sign_arr ['gameuid'],$sign_arr ['uid'] );
				$accountMC->setServer($this->server);
				$this->user_account = $accountMC->validate ( $param, $sign_arr );
			}
			$this->sns_id = $sign_arr ['sns_id'];
			$this->gameuid = $sign_arr ['gameuid'];
			$this->version = $sign_arr['version'];
			
			if (isset ( $this->gameuid )) {
				//$this->updateStatic ($this->gameuid);
			}
			
			
			$this->uid = $sign_arr ['uid'];
			if ($this->sns_id == 1) {
				if ($this->uid < 0) {
					$this->uid += pow ( 2, 32 );
				}
			}
			
			$this->commandName = $command;
			$this->current_command = $command;
			$this->setTimezone ();
			
			// intval参数中的数值参数
// 			if ($param) {
// 				$params = $this->transformNum ( $param );
// 			}
			
			$re ['ecode'] = 0;
			$re ['data'] = array ();
			$re ['cmd'] = $command;
			
			// 处理合法错误
			try {
				$re ['data'] = $this->executeEx ( $param );
			} catch ( Exception $e ) {
				$errorCode = $e->getCode ();
				if ($errorCode > 100000) {
					$re ['ecode'] = $errorCode;
				} else {
					$message = '';
					$message .= '[line]' . $e->getLine () . '/r/n';
					$message .= '[file]' . $e->getFile () . '/r/n';
					$message .= '[msg]' . $e->getMessage ();
					$this->throwException ( $message, $errorCode );
				}
			}
			
			if ($param ['sync']) {
				$re ['sync'] = 1;
				//$re ['syncdata'] = $this->getSyncData ();
			} else {
				$re ['sync'] = 0;
			}
			
			//检测返回值没有null值
 			if($this->checkNull($re)){
 				//$this->writeAccessLog ( $command, $param, $sign_arr, $re ,'checkNull_logs');
 			}
			
			if (DEBUG) {
				//$this->writeAccessLog ( $command, $param, $sign_arr, $re );
			}
		} catch ( Exception $e ) {
			$re ['status'] = 0;
			$message = '';
			$message .= '[line]' . $e->getLine () . '/r/n';
			$message .= '[file]' . $e->getFile () . '/r/n';
			$message .= '[msg]' . $e->getMessage ();
			$this->throwException ( $message, $errorCode );
		}
		return $re;
	}
	
	protected function updateStatic($gameuid='')
	{
		// 根据不同的用户，进行不同的实验
		$mod = $gameuid % 10;
		$str = 'static/';
		if (in_array ( $mod, array(1) )) {
			$str = 'static1/';
		} else if (in_array ( $mod, array(2) )) {
			$str = 'static2/';
		}
		$GLOBALS ['config'] ['cdnBase'] = str_replace ( 'static/', $str, $GLOBALS ['config'] ['cdnBase'] );
		$GLOBALS ['config'] ['quest_xmlpath'] = str_replace ( 'static/', $str, $GLOBALS ['config'] ['quest_xmlpath'] );
		$this->questrStr = $str;
	}
	/**
	 *
	 *
	 * 同步数据,按照类别同步
	 * 暂时还没有按照类别启用
	 *
	 * @param $types array
	 */
	protected function getSyncData($types = array()) {
		$syncData = array ();
		$accountMC = $this->createAccountMC ();
		include_once PATH_CACHE . 'InvadeCache.php';
		$syncData ['invide']=array();
		$syncData ['pvpharvest'] = 0;
			// 只有在有好友的方法中才返回
// 		if (substr ( get_class ( $this ), 0, 6 ) == 'Friend' && false) {
// 			$invadeMC = new InvadeCache ( $this->gameuid );
// 			$invadeMC->setServer ( $this->server );
// 			$syncData ['invide'] = $invadeMC->getList ();
// 			$syncData ['pvpharvest'] = 0;
// 			foreach ( $syncData ['invide'] as $key => $value ) {
// 				if (time () - $value ['updatetime'] >= 60 * 60 * 2 && time () - $value ['createtime'] < 60 * 60 * 2 * 24) {
// 					$syncData ['pvpharvest'] = 1;
// 					break;
// 				}
// 			}
// 		}
		$accountInfo = $accountMC->getAccount ();
		// 更新用户体力
		// $powerInfo =$accountMC->getPowerCurrent(false);
		// $syncData['havestcount']=$havestCount;
		$power = $accountMC->getPowerCurrent ( false );
		$syncData ['power'] = $power ['power'];
		$syncData ['powerupdatetime'] = $power ['powerupdatetime'];
		$syncData ['pvppower'] = $power ['pvppower'];
		$syncData ['pvppowerupdatetime'] = $power ['pvppowerupdatetime'];
		// floor((time()-$accountInfo['powerupdatetime'])/18)+$syncData['power'];
		// $accountInfo = $this->issync ? $accountMC->getAccount() :
		// $accountMC->updateUserResource();
		$syncData ['resource'] = array ('crop' => $accountInfo ['crop'] );
		// $syncData['growth'] = $accountMC->getUesrResourceGrowth();
		$soldierMC = $this->createSoldierMC ();
		$syncData ['soldier'] = $soldierMC->getSoldierCount ();
		$syncData ['soldier_idle'] = $soldierMC->getSoldierIdleCount ();
		$syncData ['coin'] = $accountInfo ['coin'];
		$syncData ['points'] = $accountInfo ['points'];
		$syncData ['population'] = $soldierMC->getAllSoldierCount ();
		$syncData ['level'] = $accountInfo ['level'];
		$syncData ['exp'] = $accountInfo ['exp'];
		$syncData ['leveluppoints'] = $accountInfo ['leveluppoints'];
		include_once PATH_CACHE . 'MessageCache.php';
		$messageMC = new MessageCache ( $this->gameuid );
		$syncData ['friendinfocount'] = $messageMC->getInfoCount ( $this->gameuid );
		$syncData ['warinfocount'] = $messageMC->getWarInfoCount ( $this->gameuid );
		$syncData ['beinvade'] = 0;
		$syncData ['beinvadelevel'] = 0;
		$syncData ['beinvadeInfo'] = array ('photourl'=>'','level'=>0,'displayname'=>'');
		if ($accountInfo ['invadeby'] > 0 && time () - $accountInfo ['invadetime'] < 60 * 60 * 24 * 2) {
			$syncData ['beinvade'] = $accountInfo ['invadeby'];
			$accountMC2 = $this->createAccountMC ( $accountInfo ['invadeby'] );
			$accountInfo2 = $accountMC2->getAccount ();
			$syncData ['beinvadeInfo'] ['photourl'] = $accountInfo2 ['photourl'];
			$syncData ['beinvadeInfo'] ['level'] = $accountInfo2 ['level'];
			$syncData ['beinvadeInfo'] ['displayname'] = $accountInfo2 ['displayname'];
		}
		return $syncData;
	}
	
	
	public function setGameuid($gameuid) {
		$this->gameuid = $gameuid;
	}
	public function setUid($uid) {
		$this->uid = $uid;
	}
	/**
	 *
	 *
	 * accesslog按照每天记录一份所有command的log
	 *
	 * @param $command unknown_type
	 * @param $params unknown_type
	 * @param $sig_arr unknown_type
	 * @param $result unknown_type
	 */
	protected function writeAccessLog($command, $params, $sig_arr, $result, $model = 'access_logs') {
		$log_dir = $GLOBALS ['config'] ['log_path'] . $model . '/' . date ( 'Ymd' );
		$filePath = $log_dir . '/' . $command . '.log';
		if (! file_exists ( $GLOBALS ['config'] ['log_path'] .$model )) {
			mkdir ( $GLOBALS ['config'] ['log_path'] . $model, 0777, true );
		}
		if (! file_exists ( $log_dir )) {
			mkdir ( $log_dir, 0777, true );
		}
		$date = date ( 'Y-m-d H:i:s', time () );
		$message = array ('date' => $date, 'commandName' => $command, 'params' => $params, 'sig' => $sig_arr, 'result' => $result );
		file_put_contents ( $filePath, print_r ( $message, TRUE ), FILE_APPEND );
	}
	// 转换参数中的数字字符串
	protected function transformNum($param) {
		foreach ( $param as $key => $v ) {
			if (is_numeric ( $v ) && $key != 'attackgameuid') {
				$param [$key] = intval ( $v );
			}
			if (is_array ( $v ) || is_object ( $v )) {
				$param [$key] = $this->transformNum ( $v );
			}
		
		}
		return $param;
	}
	//检测返回值中的null值
	protected function checkNull($data){
		$haveNull = false;
		if(is_array($data)){
			foreach ($data as $key => $value){
				if(is_null($value)){
					//TODO::如果判断为null,写入log
					//$data[$key] = '';
					$haveNull = true;
				}else if(is_array($value)){
					$haveNull = $this->checkNull($value);
				}
			}
		}else{
			if(is_null($data)){
				$haveNull = true;
			}
		}
		
		return $haveNull;
	}
	/**
	 *
	 *
	 * 建立 Runtime model 操作对象
	 *
	 * @param $gameuid int
	 * @param $uid string
	 * @param $new bool
	 * @return RunTimesCache
	 */
	protected function createRunTimesModel($gameuid = null, $uid = null, $new = FALSE) {
		if (empty ( $gameuid )) {
			$gameuid = $this->gameuid;
		}
		if (empty ( $this->runTimesObject ) || ! array_key_exists ( $gameuid, $this->runTimesObject )) {
			include_once PATH_CACHE . 'RunTimesCache.php';
			$this->runTimesObject [$gameuid] = new RunTimesCache ( $gameuid, $uid );
		}
		return $this->runTimesObject [$gameuid];
	}
	protected function createMarchMC($gameuid = null, $uid = null, $new = false) {
		if (empty ( $gameuid )) {
			$gameuid = $this->gameuid;
		}
		if (empty ( $this->marchObject ) || ! array_key_exists ( $gameuid, $this->marchObject )) {
			include_once PATH_CACHE . 'MarchCache.php';
			
			$this->marchObject [$gameuid] = new MarchCache ( $gameuid, $uid );
		}
		return $this->marchObject [$gameuid];
	}
	/**
	 *
	 *
	 * 建立 建筑物 model 操作对象
	 *
	 * @param $gameuid int
	 * @param $uid string
	 * @param $new bool
	 * @return BuildingCache
	 */
	protected function createBuildingMC($gameuid = null, $uid = null, $new = false) {
		if (empty ( $gameuid )) {
			$gameuid = $this->gameuid;
		}
		if (empty ( $this->buildingObject ) || ! array_key_exists ( $gameuid, $this->buildingObject )) {
			include_once PATH_CACHE . 'BuildingCache.php';
			
			$this->buildingObject [$gameuid] = new BuildingCache ( $gameuid, $uid );
		}
		return $this->buildingObject [$gameuid];
	}

	protected function updateRuntimes($iscommit = false) {
		$runTimesMC = $this->createRunTimesModel ( $this->gameuid, $this->uid );
		$sucessList = $runTimesMC->handlerRunTimes ( true );
		
		if ($iscommit) {
			if (array_key_exists ( 'upgrade', $sucessList )) {
				$buildingMC = $this->createBuildingMC ( $this->gameuid, $this->uid );
				$buildingMC->updateContent ( $sucessList ['upgrade'] );
			}
			if (array_key_exists ( 'produce', $sucessList )) {
				$soldierMC = $this->createSoldierMC ( $this->gameuid, $this->uid );
				$soldierMC->updateFree ( $sucessList ['produce'] );
			}
		}
		return $sucessList;
	}
		/**
	 *
	 *
	 * 建立 Item model 操作对象
	 *
	 * @param $gameuid int
	 * @param $uid string
	 * @param $new bool
	 * @return ItemCache
	 */
	protected function createItemMC() {
		if (empty ( $this->itemObject )) {
			include_once PATH_CACHE . 'ItemCache.php';
			$this->itemObject = new ItemCache ();
		}
		return $this->itemObject;
	}
	/**
	 * 设置程序默认时区
	 */
	protected function setTimezone() {
		if (isset ( $GLOBALS ['config'] ['sns_arr'] [$this->sns_id] )) {
			date_default_timezone_set ( $GLOBALS ['config'] ['sns_arr'] [$this->sns_id] ['timezone'] );
		} else {
			date_default_timezone_set ( 'Asia/Shanghai' );
		}
	}
	
	/**
	 * 建立account cache操作对象
	 *
	 * @param
	 *       	 int gameuid
	 * @param
	 *       	 string uid
	 * @return AccountCache
	 */
	protected function createAccountModel($gameuid, $uid = null, $new = false) {
		return $this->createAccountMC ( $gameuid, $uid, $new );
	}
	/**
	 * 建立account cache操作对象
	 *
	 * @param
	 *       	 int gameuid
	 * @param
	 *       	 string uid
	 * @return AccountCache
	 */
	protected function createAccountMC($gameuid = null, $uid = null, $new = false) {
		if (empty ( $gameuid )) {
			$gameuid = $this->gameuid;
		}
		if (empty ( $this->accountObject ) || ! array_key_exists ( $gameuid, $this->accountObject )) {
			require_once PATH_CACHE . 'AccountCache.php';
			$this->accountObject [$gameuid] = new AccountCache ( $gameuid, $uid );
		}
		return $this->accountObject [$gameuid];
	}
	/**
	 * 检查用户账户
	 *
	 * @param $field array
	 *       	 示例:array('coin'=>-50,'exp'=>-33,....)
	 */
	protected function checkUserStatus($field = array()) {
		$a = array ('population' );
		$account = $this->createAccountModel ( $this->gameuid, $this->uid );
		if (empty ( $this->user_account )) {
			$this->user_account = $account->getAccount ();
		}
		// 计算各种资源的收益
		// $this->updateResource();
		$change = array ();
		foreach ( $field as $key => $val ) {
			if (in_array ( $key, $a )) {
				if ($val + $this->user_account [$key] > $this->user_account ['populationmax']) {
					$this->throwException ( "$key is more than populationmax", 104 );
				}
				if ($val + $this->user_account [$key] < 0) {
					$this->throwException ( "$key is less than 0", 101 );
				}
			} elseif ($key == 'level') {
			
			} else {
				// 资源部分数值后五位为小数位
				$b = $this->createAccountMC ()->getResourceFields ();
				if ($val + $this->user_account [$key] < 0) {
					$this->licitException ( "$key is not enough", 101 );
				
				}
			}
		}
	}
	
	protected function executeEx($params) {
	
	}
	
	
	/**
	 * 更新用户账户
	 *
	 * @param $change array
	 */
	protected function updateUserStatus($change = array(), $relatedId = '', $gameuid = '', $uid = '') {
		if (empty ( $gameuid )) {
			$gameuid = $this->gameuid;
		}
		// 更新资源字段的时候需要扩大1000倍
		$handlerArray = $this->createAccountMC ()->getResourceFields ();
		foreach ( $handlerArray as $value ) {
			if (! array_key_exists ( $value, $change )) {
				$change [$value] = 0;
			}
		}
		$account = $this->createAccountModel ( $gameuid, $uid );
		if (empty ( $this->user_account )) {
			$this->user_account = $account->getAccount ();
		}

		
		if ($change ['crop'] > 0) {
			$temvalue = $change ['crop'] + $this->createAccountMC ()->getAccountFiled ( 'crop' );
			if ($temvalue > $this->createAccountMC ()->getMaxResourses ()) {
				$change ['crop'] = $this->createAccountMC ()->getMaxResourses () - $this->createAccountMC ()->getAccountFiled ( 'crop' );
			}
		}
	
		$logarr = array ('crop', 'coin', 'points','power','pvppower');
		if (count ( array_intersect ( $logarr, array_keys ( $change ) ) ) > 0) {
			require_once PATH_CACHE . 'UpdateLogCache.php';
			$updateLogMC = new UpdateLogCache ( $gameuid );
			$temarr = array ();
			//file_put_contents(PATH_ROOT . 'change.log', print_r($change,TRUE),FILE_APPEND);
			foreach ( $change as $changeKey => $changeValue ) {
				if($changeKey == 'powerupdatetime' || $changeKey == 'pvppowerupdatetime'){
					continue;
				}
				if ($changeValue != 0 && in_array ( $changeKey, $logarr )) {

					if($changeKey == 'power'){
						$temarr [] = array ('gameuid' => $this->gameuid, 'createtime' => time (), 'type' => $account->getIdFromType ( $changeKey ), 'cost' => -30, 'remain' => $changeValue, 'command' => get_class ( $this ) );
					}else if ($changeKey == 'pvppower'){
						$temarr [] = array ('gameuid' => $this->gameuid, 'createtime' => time (), 'type' => $account->getIdFromType ( $changeKey ), 'cost' => -200, 'remain' => $changeValue, 'command' => get_class ( $this ) );
					}else{
						$temarr [] = array ('gameuid' => $this->gameuid, 'createtime' => time (), 'type' => $account->getIdFromType ( $changeKey ), 'cost' => $changeValue, 'remain' => $this->createAccountMC ()->getAccountFiled ( $changeKey ) + $changeValue, 'command' => get_class ( $this ) );
					}
				}
					// 如果是腾讯的版本，进行如下操作
				if ($changeKey == 'points' && $this->sns_id == 1 && ! $this->isSyncPoints && $this->isQQPoints) {
					if ($changeValue < 0) {
						$ret = $this->paymentQQ ( $changeValue );
						// 如果没有成功的话
						if ($ret ['ret'] != 0) {
							$change [$changeKey] = 0;
						}
					}
				}
			}
			if (! empty ( $temarr )) {
				$updateLogMC->setInfo($this->user_account['source'],$this->user_account['level']);
				$updateLogMC->setUid($this->uid);
				$updateLogMC->addarr ( $temarr );
			}
		}
			// 如果升级,更新level
		if ($change ["exp"] > 0) {
			$itemMC = $this->createItemMC ();
			$result = array ('last_exp' => $this->user_account ["exp"], 'add_exp' => $change ["exp"], 'now_exp' => $change ["exp"] + $this->user_account ["exp"], 'last_level' => $this->user_account ['level'], 'now_level' => $this->user_account ['level'] );
			while ( $result ['now_level'] < 30 ) {
				$itemRes = $itemMC->selectItem ( 'Account', $result ['now_level'] + 1 );
				$need_exp = intval ( $itemRes ['exp'] );
				if ($need_exp == 0 || $result ['now_exp'] < $need_exp) {
					break;
				}
				$result ['now_exp'] -= $need_exp;
				$result ['now_level'] ++;
			}
			$change ['exp'] = $result ['now_exp'];
			if ($result ['now_level'] > $this->user_account ['level']) {
				$itemInfo = $itemMC->selectItem ( 'Account', $result ['now_level'] );
				$change ['power'] = $itemInfo ['power_max'];
				$change ['powerupdatetime'] = time ();
				$change ['pvppower'] = $itemInfo ['power_max'];
				$change ['pvppowerupdatetime'] = time ();
				$change ['level'] = $result ['now_level'];
				$change ['populationmax'] = $itemInfo ['soldier_max'];
				$this->createAccountMC ()->updateMaxResourses ( $itemInfo ['capacity'] );
			}
		}else {
			unset ( $change ['exp'] );
		}

		$res = $account->updateAccount ( $change, $this->user_account );
		// 更新runtimes
			// $this->updateRuntimes(true);
		try {

		} catch ( Exception $e ) {
		
		}
		return $res;
	}
	

	
	protected function readParam($param) {
		return false;
	}
	
	
	protected function licitException($message, $code, $uid = null, $gameuid = null) {
		$commandName = $this->commandName;
		// 设置errorcode位移
		$code += 100000;
		GameException::throwException ( $message, $code, $uid, $gameuid, true, $commandName );
	}
	/**
	 * service层异常处理
	 *
	 * @param $message unknown_type
	 * @param $code unknown_type
	 * @param $uid unknown_type
	 * @param $farmuid unknown_type
	 */
	protected function throwException($message, $code, $uid = null, $gameuid = null) {
		$commandName = $this->commandName;
		GameException::throwException ( $message, $code, $uid, $gameuid, true, $commandName );
	}
	
	// PHP整站防注入程序，需要在公共文件中require_once本文件
	// 判断magic_quotes_gpc状态
	/**
	 * 检查参数 防止sql注入
	 *
	 * @param $array unknown_type
	 */
	function sec(&$array,$lengh = 500) {
			
			// 如果是数组，遍历数组，递归调用
		if (is_array ( $array )) {
			foreach ( $array as $k => $v ) {
				$array [$k] = $this->sec ( $v );
			}
		
		} else if (is_string ( $array )) {
			// 使用addslashes函数来处理
			if (strlen ( $array ) > $lengh) {
				$this->throwException ( 'string is lengh than ' . $lengh, 124 );
			}
			$array = $this->str_check ( addslashes ( $array ) ); // 特殊字符增加反斜杠，并检查
		} else if (is_numeric ( $array )) {
			$array = $this->num_check ( intval ( $array ) );
		}
		return $array;
	}
	
	// 整型过滤函数
	function num_check($id) {
		if (! $id) {
			//$this->licitException ( 'param can not be null', 1001 );
		} 		// 是否为空的判断
		else if ($this->inject_check ( $id )) {
			$this->licitException ( 'error param', 1002 );
		} 		// 注入判断
		else if (! is_numeric ( $id )) {
			$this->licitException ( 'error param', 1002 );
		} // 数字判断
		$id = intval ( $id ); // 整型化
		return $id;
	}
	
	// 字符过滤函数
	function str_check($str) {
		if ($this->inject_check ( $str )) {
			$this->licitException ( 'error param', 1002 );
		} // 注入判断
		  // $str = htmlspecialchars($str);//转换html
		return $str;
	}
	
	function search_check($str) {
		$str = str_replace ( '_', '\_', $str ); // 把"_"过滤掉
		$str = str_replace ( '%', '\%', $str ); // 把"%"过滤掉
		$str = htmlspecialchars ( $str ); // 转换html
		return $str;
	}
	
	// 防注入函数
	function inject_check($sql_str) {
		return @eregi ( 'select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|UNION|into', $sql_str ); // 进行过滤，防注入
	}
	
	// 清理反斜杠
	function stripslashes_array(&$array) {
		if (is_array ( $array )) {
			foreach ( $array as $k => $v ) {
				$array [$k] = $this->stripslashes_array ( $v );
			}
		} else if (is_string ( $array )) {
			$array = stripslashes ( $array );
		}
		return $array;
	}
	
	protected function addAnalysisItem($cache) {
		$this->cacheItem [] = $cache;
	}
	protected function saveAnalysisCount()
	{
		$insert =0;
		$del =0;
		$select=0;
		$update = 0;
		$modelArr = array ();
		foreach ( $this->cacheItem as $key => $value ) {
			$temArr = $value->commandAnalysis ();
			$insert += $temArr ['insert'];
			$del += $temArr ['del'];
			$select += $temArr ['select'];
			$update += $temArr ['update'];
			$modelArr [$temArr ['model']] ['insert'] += $temArr ['insert'];
			$modelArr [$temArr ['model']] ['del'] += $temArr ['del'];
			$modelArr [$temArr ['model']] ['select'] += $temArr ['select'];
			$modelArr [$temArr ['model']] ['update'] += $temArr ['update'];
		}
		include_once PATH_CACHE . 'CommandAnalysisCache.php';
		$comAnaMC=new CommandAnalysisCache($this->gameuid);
		$insertArr=array('gameuid'=>$this->gameuid,
				'createtime'=>time(),
				'command'=>get_class($this),
				'model'=>json_encode($modelArr),
				'modelcount'=>count($this->cacheItem),
				 'insertCount' => $insert,
				 'updateCount' => $update,
				'selectCount' => $select,
				 'delCount' => $del);
		$comAnaMC->addarr(array($insertArr) );
	}
	
	protected function comm_prepay_base($amt) {
		$qqpay = $this->initQQPay ();
		return $qqpay->comm_prepay ( $amt );
	}
	
	protected function qz_get_balance() {
		$this->initQQPay ();
		$ret = $this->qqPayObject->qz_get_balance ();
		if ($ret ['ret'] == 0) {
			return $ret ['balance'];
		} else {
			return - 1;
		}
	}
	
	protected function comm_prepay_confirm($amt, $billno, $action = 'confirm') {
		$this->initQQPay ();
		$ret = $this->qqPayObject->comm_prepay_confirm ( $amt, $billno, $action );
		return $ret;
	}
	
	protected function initQQPay() {
		if (empty ( $this->qqPayObject )) {
			require_once FRAMEWORK.'/platform/qq/qqPay.php';
			$this->qqPayObject = new qqPay ();
			$this->qqPayObject->setOpenIdOpenKey ( $this->uid, $this->session_key );
			$this->qqPayObject->setAppSource ( get_class ( $this ) );
			$this->qqPayObject->setPayItem ( get_class ( $this ) );
		}
		return $this->qqPayObject;
	}
	
	protected function paymentQQ($amt) {
		$amt = abs ( $amt );
		$ret = $this->comm_prepay_base ( $amt );
		if ($ret ['ret'] == 0) {
			// 订单号
			$billno = $ret ['billno'];
			try {
				$res = $this->comm_prepay_confirm ( $amt, $billno, 'confirm' );
// 				file_put_contents( PATH_ROOT.'/payreturn.log', print_r($res,true), FILE_APPEND);
				if (! isset ( $res ['ret'] ) || $ret ['ret'] != 0) {
					$this->contentError ();
				}
			} catch ( Exception $ex ) {
				// 验证购买无效
				$this->comm_prepay_confirm ( $amt, $billno, 'cancel' );
				$this->contentError ();
			}
		} else {
			$this->contentError ();
			return $ret;
		}
	}
	
	protected function writeLog($content){
		//file_put_contents( PATH_ROOT.'/paymenterror.log', print_r($content,true), FILE_APPEND);
	}
	protected function contentError() {
		$this->licitException ( 'content error', 1500 );
	}
}

?>