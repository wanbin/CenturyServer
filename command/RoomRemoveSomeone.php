<?php
// 删除某个玩家
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class RoomRemoveSomeone extends BaseCommand {
	protected function executeEx($params) {
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$delgameuid=$params['gameuid'];
		$ret= $room->delSomeOne($delgameuid);
		return $this->reutrnDate ( COMMAND_SUCCESS,$ret );
	}
}