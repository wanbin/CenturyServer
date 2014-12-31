<?php
/**
 * @author WanBin @date 2013-08-03
 * 微信用户表
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_MODEL . 'WXModel.php';
class WXCache extends WXModel{
	public function getMessageCount($msg) {
		$rediska = new Rediska ();
		$list = new Rediska_Key_SortedSet ( "wx_message_send_count" );
		$list->incrementScore ( $msg, 1 );
		return $list->getScore ( $msg );
	}
	
	public function getMessageList() {
		$rediska = new Rediska ();
		$list = new Rediska_Key_SortedSet ( "wx_message_send_count" );
		$ret= $list->getByRank(true,0,100,true,false);
		$result=array();
		$rank=1;
		foreach ($ret as $key=>$value){
			$result[]=array('value'=>$value['value'],'score'=>$value['score'],'rank'=>$rank);
			$rank++;
		}
		return $result;
	}
	public function delKey($key){
		$rediska = new Rediska ();
		$list = new Rediska_Key_SortedSet ( "wx_message_send_count" );
		$list->remove($key);
	}
}