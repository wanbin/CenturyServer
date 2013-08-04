<?php
/**
  * wechat php test
  */
include_once '../Entry.php';
//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
 $wechatObj->responseMsg();

class wechatCallbackapiTest
{
	//public $echo=false;
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = $this->returncontent($keyword,$fromUsername);
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	public function returncontent($keyword,$uid) {
	include_once PATH_DATAOBJ . "/cache/UnderCoverCache.php";
		$UnderCache = new UnderCoverCache ( $uid );
		$UnderCache->Log ( $keyword );
		$type = intval ( $keyword );
		if ($type > 3 && $type < 15) {
			$gameuid = $UnderCache->gameuid;
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ();
			$UnderRoomCache->echoit = $this->echo;
			$str= $UnderRoomCache->initRoom ( $type, $gameuid );
			if ($this->echo) {
				echo $str;
			}
			return $str;
		} else if ($type == 1) {
			$str = "创建谁是卧底游戏成功：请输入4-14参与人数（不包括法官）：";
			if ($this->echo) {
				echo $str;
			}
			return $str;
		} else if ($type == 2) {
			$str = "【测试】创建狼人杀游戏成功：请输入4-14参与人数（不包括法官）：";
			if ($this->echo) {
				echo $str;
			}
			return $str;
		} else if ($type == 3) {
			$str = "【测试】创建杀人游戏成功：请输入4-14参与人数（不包括法官）：";
			if ($this->echo) {
				echo $str;
			}
			return $str;
		} else if ($type == 20) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ();
			$str = $UnderRoomCache->getChengfa ( $type, $gameuid );
			if ($this->echo) {
				echo $str;
			}
			return $str;
		} else if ($type >= 1000) {
			$gameuid = $UnderCache->gameuid;
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ();
			$str = $UnderRoomCache->getInfo ( $type, $gameuid );
			if ($this->echo) {
				echo $str;
			}
			return $str;
		} else {
			$str="请您选择项目：\n 4-14 创建谁是卧底游戏: \n 输入 20 返回真心话大冒险：";
			if($this->echo)
			{
				echo $str;
			}
			return $str;
		}
	}
}

?>