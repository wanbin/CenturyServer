<?php
/**
  * wechat php test
  */
include_once '../Entry.php';
// define your token
define ( "TOKEN", "weixin" );
$wechatObj = new wechatCallbackapiTest ();
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
			$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
			$this->fromUsername = $postObj->FromUserName;
			$this->toUsername = $postObj->ToUserName;
			$this->event = $postObj->Event;
			$keyword = trim ( $postObj->Content );
			$messageType = $postObj->MsgType;
				
				// 如果是语音及图片，直接返回
			if (!in_array ( $messageType, array (
					"text"
			) )) {
				$this->returnMsg ( "Sorry~我们现在不能识别您发来的信息\n试着回复'?'能不能给你带来帮助\n");
			} else if (! empty ( $this->event )) {
				if ($this->event == 'subscribe') {
					include_once PATH_DATAOBJ . "/cache/UnderCoverCache.php";
					$UnderCache = new UnderCoverCache ( $this->fromUsername );
					$contentStr = $UnderCache->returncontent ( 'help' );
					$this->returnMsg ( $contentStr );
				}
			} else if (! empty ( $keyword )) {
				include_once PATH_DATAOBJ . "/cache/UnderCoverCache.php";
				$UnderCache = new UnderCoverCache ( $this->fromUsername );
				$contentStr = $UnderCache->returncontent ( $keyword );
				$this->returnMsg ( $contentStr );
			} else {
				echo "Input something...";
			}
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