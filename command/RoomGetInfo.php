<?php
// 取得一个房间的信息
include_once 'BaseCommand.php';
include_once 'handler/PublishHandler.php';
class RoomGetInfo extends BaseCommand {
	protected function executeEx($params) {
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$roomid=$room->getGameuid($this->uid);
		$ret=$room->GetRoomInfo ($roomid);
		return $this->reutrnDate ( COMMAND_ENPTY,$ret );
	}
}