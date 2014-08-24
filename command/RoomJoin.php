<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class RoomJoin extends BaseCommand {
	protected function executeEx($params) {
		$roomid = $params ['roomid'];
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$ret = $room->JoinRoom ( $roomid-10000 );
		if ($ret) {
			return $this->reutrnDate ( COMMAND_SUCCESS,array('roomid'=>$roomid) );
		} else {
			return $this->reutrnDate ( ERROR_ROOM );
		}
		
	}
}