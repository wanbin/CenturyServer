<?php
/**
 * @author WanBin @date 2014-01-14
 * 用户行为LOG
 */
require_once PATH_MODEL.'BaseModel.php';
class MailModel extends BaseModel {
	

	protected function getOneMail(){
		$where = array (
				'gameuid' => $this->gameuid,
// 				'read'=>0
		);
		$ret=$this->getOneFromMongo( $where, 'mail',-1 );
		//如果第一条都被读了，那就返回吧
		if($ret['read']==1){
			return array();
		}
		$this->readMail($ret['_id']);
		return $ret;
	}
	protected function getMailList($page){
		$where = array (
				'gameuid' => $this->gameuid,
		);
		$pageNum = 30;
		$skip = $pageNum * ($page-1);
		$ret= $this->getFromMongo ( $where, 'mail', array("_id"=>-1),$skip,$pageNum );
		return $ret;
	}
	
	
	protected function SendMail($from, $sendto, $content) {
		$content = array (
				'gameuid' => $sendto,
				'fromgameuid' => $from,
				'content' => $content,
				'read'=>0
		);
// 		print_R($content);
		$id = $this->insertMongo ( $content, 'mail' );
// 		echo $id;
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
	
	protected function mailDel($id) {
		if ($id > 0) {
			$where=array('_id'=>intval($id));
			$this->removeMongo($where, 'mail');
			return true;
		}
		return false;
	}
}