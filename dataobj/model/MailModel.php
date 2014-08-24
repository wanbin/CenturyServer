<?php
/**
 * @author WanBin @date 2014-01-14
 * 用户行为LOG
 */
require_once PATH_MODEL.'BaseModel.php';
class MailModel extends BaseModel {
	

	protected function getOneMail(){
		$where = array (
				'gameuid' => $this->gameuid
		);
		$ret=$this->getOneFromMongo( $where, 'mail',1 );
		$this->readMail($ret['_id']);
		return $ret;
	}
	
	protected function SendMail($from, $sendto, $content) {
		$content = array (
				'gameuid' => $sendto,
				'fromgameuid' => $from,
				'content' => $content 
		);
		$id = $this->insertMongo ( $content, 'mail' );
		return $id;
	}
	
	protected function readMail($id) {
		if ($id > 0) {
			$content=array('read'=>1);
			$where=array('_id'=>intval($id));
			$this->updateMongo($content, $where, 'mail');
			return true;
		}
		return false;
	}
}