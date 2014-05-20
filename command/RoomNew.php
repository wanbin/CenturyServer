<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/PublishHandler.php';
class RoomNew extends BaseCommand {
	protected function executeEx($params) {
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$room->NewRoom ();
		return $this->reutrnDate ( COMMAND_SUCCESS );
	}
}