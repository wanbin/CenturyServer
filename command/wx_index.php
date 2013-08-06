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
			$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$event=$postObj->Event;
			$keyword = trim ( $postObj->Content );
			$time = time ();
			$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
			if(!empty($event))
			{
				if($event=='subscribe')
				{
					$msgType = "text";
					include_once PATH_DATAOBJ . "/cache/UnderCoverCache.php";
					$UnderCache = new UnderCoverCache ( $fromUsername );
					$contentStr = $UnderCache->returncontent ( 'help' );
					$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
					echo $resultStr;
				}
			}
			else if (! empty ( $keyword )) {
				$msgType = "text";
				include_once PATH_DATAOBJ . "/cache/UnderCoverCache.php";
				$UnderCache = new UnderCoverCache ( $fromUsername );
				$contentStr = $UnderCache->returncontent ( $keyword );
				$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
				echo $resultStr;
			} else {
				echo "Input something...";
			}
		} else {
			echo "";
			exit ();
		}
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