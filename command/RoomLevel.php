<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class RoomLevel extends BaseCommand {
	protected function executeEx($params) {
		$roomid = $params ['roomid'];
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$ret = $room->LevelRoom();
		if ($ret) {
			return $this->returnDate ( COMMAND_SUCCESS );
		} else {
			return $this->returnDate ( ERROR_ROOM );
		}
		
	}
}