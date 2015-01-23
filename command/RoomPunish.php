<?php
// 这个是玩家游戏结束之后接受惩罚的接口
include_once 'BaseCommand.php';
include_once 'handler/PunishHandler.php';
class RoomPunish extends BaseCommand {
	protected function executeEx($params) {
		include_once PATH_HANDLER . 'RoomsHandler.php';
		$room = new RoomsHandler ( $this->uid );
		$gameuidarr= explode("_", trim($params['gameuidstr'],'_'));
		$ret= $room->PunishSomeOne($gameuidarr);
		return $this->returnDate ( COMMAND_SUCCESS,array('punish'=>$ret));
	}
}