<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class RoomNew extends BaseCommand {
	protected function executeEx($params) {
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$roomid=$room->NewRoom ();
		return $this->returnDate ( COMMAND_SUCCESS,array('roomid'=>$roomid) );
	}
}