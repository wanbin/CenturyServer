<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class RoomJoin extends BaseCommand {
	protected function executeEx($params) {
		$roomid = $params ['roomid'];
		//加入自己创建的房间
		
		if($roomid==$this->gameuid){
			return $this->returnDate(ERROR_ROOM_SELF);
		}
		
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$ret = $room->JoinRoom ( $roomid );
		
		if ($ret > 0) {
			return $this->returnDate ( COMMAND_SUCCESS, array (
					'roomid' => $roomid 
			) );
		} else if ($ret == - 2) {
			return $this->returnDate ( ERROR_ROOM_NULL );
		} else if ($ret == - 1) {
			return $this->returnDate ( ROOM_NEED_PAY );
		} else {
			return $this->returnDate ( ERROR_ROOM );
		}
	}
	
}