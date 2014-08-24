<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class RoomStartGame extends BaseCommand {
	protected function executeEx($params) {
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$type=$params['type'];
		$addPeople=$params['addPeople'];
		$ret= $room->StartGame($type,$addPeople);
		return $this->reutrnDate ( COMMAND_SUCCESS,$ret );
	}
}