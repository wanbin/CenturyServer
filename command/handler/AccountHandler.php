<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户类实例
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_CACHE . 'AccountCache.php';
class AccountHandler extends AccountCache {
	public function getInfo($uid) {
		return parent::getAccountByUid ( $uid );
	}
	
	
	public function sendPushByGameuid($gameuid,$content,$addMail=false){
		if ($addMail) {
			include_once PATH_HANDLER . 'MailHandler.php';
			$mail = new MailHandler ( $this->uid );
			$mail->mailSend(-1, $gameuid, $content );
		}
		include_once PATH_HANDLER.'AccountHandler.php';
		$account = new AccountHandler ( $this->uid );
		$userInfo=$account->getAccountByGameuid ( $gameuid );
		require_once FRAMEWORK . 'jpush/jpush.php';
		$obj = new jpush ( masterSecret, appkeys );
		$msg_content = json_encode ( array (
				'n_builder_id' => 0,
				'n_title' => "谁是卧底-爱上聚会",
				'n_content' => $content
		) );
		$res = $obj->send ( rand ( 100000000, 999999999 ), 3, str_replace("-", "", $userInfo['uid']), 1, $msg_content, strtolower($userInfo['channel']) );
	}
	
	public function sendPushByTag($tag,$content){
		require_once FRAMEWORK . 'jpush/jpush.php';
		$obj = new jpush ( masterSecret, appkeys );
		$msg_content = json_encode ( array (
				'n_builder_id' => 0,
				'n_title' => "爱上聚会",
				'n_content' => $content
		) );
		$res = $obj->send ( rand ( 100000000, 999999999 ), 2, $tag, 1, $msg_content, 'android,ios' );
	}
	/**
	 * 玩家发送GM推送
	 * @param unknown_type $useralise
	 * @param unknown_type $content
	 */
	public function sendJPush($useralise, $content) {
		require_once FRAMEWORK . 'jpush/jpush.php';
		$obj = new jpush ( masterSecret, appkeys );
		$msg_content = json_encode ( array (
				'n_builder_id' => 0,
				'n_title' => "谁是卧底-爱上聚会",
				'n_content' => $content
		) );
		$res = $obj->send ( rand ( 100000000, 999999999 ), 3, str_replace("-", "", $useralise), 1, $msg_content, "android,ios" );
	}
	public function changeUserName($username) {
		parent::updateUserName($username);
	}
}