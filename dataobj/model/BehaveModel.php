<?php
/**
 * @author WanBin @date 2014-01-12
 * 用户行为LOG
 */
require_once PATH_MODEL.'BaseModel.php';
class BehaveModel extends BaseModel {
	
	protected function add($behave) {
		$gameuid=$this->gameuid;
		$time=time();
		$sql="insert into user_behave(gameuid,behave,time) values($gameuid,'$behave',$time)";
		return $this->oneSql($sql);
	}
	
}