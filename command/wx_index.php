<?php
/**
  * wechat php test
  */
include_once '../define.php';
// define your token
define ( "TOKEN", "weixin" );
$wechatObj = new wechatCallbackapiTest ();
if (false) {
	$wechatObj->valid ();
}
$wechatObj->responseMsg ();
class wechatCallbackapiTest {
	private $fromUsername = "";
	private $toUsername = "";
	private $msgType = "text";
	private $event = "";
	public function valid() {
		$echoStr = $_GET ["echostr"];
		if ($this->checkSignature ()) {
			echo $echoStr;
			exit ();
		}
	}
	public function responseMsg() {
		// get post data, May be due to the different environments
		$postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];
		
		// extract post data
		if (! empty ( $postStr )) {
			$postObj = (array)simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
			$this->fromUsername = $postObj['FromUserName'];
			$this->toUsername = $postObj['ToUserName'];
			$this->event = $postObj['Event'];
			$keyword = trim ( $postObj['Content'] );
			$messageType = $postObj['MsgType'];
			if ($this->event == 'subscribe') {
				$keyword = 'ATTENTION';
			} else if ($messageType != 'text') {
				$keyword = 'ERROR';
			} 
			
			include_once PATH_HANDLER . "/WXHandler.php";
			$UnderCache = new WXHandler ( $this->fromUsername );
			$contentStr = $UnderCache->returncontent ( $keyword );
			$this->returnMsg ( $contentStr );
		} else {
			echo "";
			exit ();
		}
	}
	public function returnMsg($msg) {
		$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
		$resultStr = sprintf ( $textTpl, $this->fromUsername, $this->toUsername, time (), $this->msgType, $msg );
		echo $resultStr;
	}
	private function checkSignature() {
		$signature = $_GET ["signature"];
		$timestamp = $_GET ["timestamp"];
		$nonce = $_GET ["nonce"];
		
		$token = TOKEN;
		$tmpArr = array (
				$token,
				$timestamp,
				$nonce
		);
		sort ( $tmpArr );
		$tmpStr = implode ( $tmpArr );
		$tmpStr = sha1 ( $tmpStr );
		
		if ($tmpStr == $signature) {
			return true;
		} else {
			return false;
		}
	}
}

?>