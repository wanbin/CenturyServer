<?php
// 新建一个房间
include_once 'BaseCommand.php';
include_once 'handler/PublishHandler.php';
class RoomStartGame extends BaseCommand {
	protected function executeEx($params) {
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$type=$params['type'];
		$ret= $room->StartGame($type);
		return $this->reutrnDate ( COMMAND_SUCCESS,array('roomid'=>$room->gameuid) );
	}
}