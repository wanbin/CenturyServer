<?php
include_once 'GameRegister.php';
class GameLoad extends GameRegister {
	protected function executeEx($params) {
		return array('level'=>1,'crop'=>400);
	}

}