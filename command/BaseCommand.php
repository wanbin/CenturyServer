<?php
//
// 命令对象基类
//
class BaseCommand {
	protected $uid; // 用户id
	protected $gameuid;
	protected $user_account;
	protected $accountObject;
	protected $marchObject;
	protected $heroObject;
	protected $sns_id;
	protected $server;
	protected $issync = FALSE;
	protected $commandName = '';
	protected $noServerCommand;
	protected $cacheItem = array();
	protected $version = '0.0.0';
	protected $questrStr = 'quest';
	
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
				$accountMC = $this->createAccountModel ( $sign_arr ['gameuid'], $sign_arr ['uid'] );
				$accountMC->setServer ( $this->server );
				$this->user_account = $accountMC->validate ( $param, $sign_arr );
			}
			
			$this->sns_id = $sign_arr ['sns_id'];
			$this->gameuid = $sign_arr ['gameuid'];
			$this->version = $sign_arr ['version'];
			$this->uid = $sign_arr ['uid'];
			$this->commandName = $command;
			$this->setTimezone ();
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
				$re ['syncdata'] = $this->getSyncData ();
			} else {
				$re ['sync'] = 0;
			}
			
			// 检测返回值没有null值
			if ($this->checkNull ( $re )) {
				
			}
			
			if (DEBUG) {
				$this->writeAccessLog ( $command, $param, $sign_arr, $re );
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
	
	
	/**
	 * @param unknown_type $param
	 * @return number
	 */
	protected function transformNum($param) {
		foreach ( $param as $key => $v ) {
			if (is_numeric ( $v )) {
				$param [$key] = intval ( $v );
			}
			if (is_array ( $v ) || is_object ( $v )) {
				$param [$key] = $this->transformNum ( $v );
			}
		}
		return $param;
	}
	

	/**
	 * @param unknown_type $data
	 * @return boolean
	 */
	protected function checkNull($data){
		$haveNull = false;
		if(is_array($data)){
			foreach ($data as $key => $value){
				if(is_null($value)){
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
	 * 建立cache操作对象
	 *
	 * @param
	 *       	 int gameuid int server
	 * @param
	 *       	 string name
	 * @return  MC	 * 
	 */
	protected function createCacheMC($name,$gameuid,$server='')
	{
	    return DataHandler::createMC($name, $gameuid,$server='');
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
	protected function updateUserStatus($change = array()) {
		if (empty ( $gameuid )) {
			$gameuid = $this->gameuid;
		}
		
		return array();
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
}

?>