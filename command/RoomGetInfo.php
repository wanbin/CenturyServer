<?php
// 取得一个房间的信息
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class RoomGetInfo extends BaseCommand {
	protected function executeEx($params) {
		$islottery=isset($params['islottery'])?$params['islottery']:false;
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$ret=$room->GetRoomInfo (0,$islottery);
		return $this->returnDate ( COMMAND_ENPTY,$ret );
	}
}