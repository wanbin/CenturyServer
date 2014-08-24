<?php
/**
 * @author WanBin @date 2014-01-12
 * 用户行为LOG
 */
require_once PATH_MODEL.'BaseModel.php';
class BehaveModel extends BaseModel {
	
	protected function add($behave) {
		return;
		$content=array(
				'gameuid'=>$this->gameuid,
				'behave'=>$behave
				);
		return $this->insertMongo($content, 'user_behave');
	}
	
}