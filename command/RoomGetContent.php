<?php
// 取得个有房间信息
include_once 'BaseCommand.php';
class RoomGetContent extends BaseCommand {
	protected function executeEx($params) {
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$ret=$room->GetRoomInfoOne ();
		return $this->reutrnDate ( COMMAND_ENPTY,$ret );
	}
}