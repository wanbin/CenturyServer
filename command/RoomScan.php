<?php
// 房间扫描
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class RoomScan extends BaseCommand {
	protected function executeEx($params) {
		$content = $params ['content'];
		//加入自己创建的房间
		$rediska = new Rediska ();
		$list = new Rediska_Key_List ( 'Socket_Map_0' );
		$ret = array (
				'gameuid' => $content,
				'username' => $content,
				'message' => $this->gameuid."登录成功" 
		);
		$list->append ( json_encode ( $ret ) );
		
		return $this->returnDate ( COMMAND_SUCCESS, array (
				'content' => $content 
		) );
	}
	
}