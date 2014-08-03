<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/PublishHandler.php';
class RoomLevel extends BaseCommand {
	protected function executeEx($params) {
		$roomid = $params ['roomid'];
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$ret = $room->LevelRoom();
		if ($ret) {
			return $this->reutrnDate ( COMMAND_SUCCESS );
		} else {
			return $this->reutrnDate ( ERROR_ROOM );
		}
		
	}
}