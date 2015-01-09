<?php
// 包括数据层及逻辑层处理缓存层处理
include_once 'AdminBase.php';
class PushBase  extends AdminBase {
	private $onesCount=20000;
	public function newpush($content,$sendtime){
		//插入一条记录数据，再把用户查出来，放入到缓存里面
		$content = array (
				'content' => $content,
				'issend' => false,
				'reday'=>-1,
				'sendtime'=>$sendtime
		);
		$id = $this->insertMongo ( $content, 'push','century_admin' );
		return $id;		
	}
	
	public function insertPushInRedis() {
		// 取到该加入到redis里面
		$ret = $this->getOneFromMongo ( array (
				'issend' => false,
				'reday' => - 1,
				'sendtime' => array (
						'$lte' => time () 
				) 
		), 'push', 'century_admin' );
		$id = $ret ['_id'];
		if ($id == 0) {
			echo "empty";
			exit ();
		}
		// 更新一下，正在加入
		$index = 0;
		$total = 0;
		while ( true ) {
			$count = $this->insertAllUidInRedis ( $id, $index );
			$total += $count;
			if ($count < $this->onesCount) {
				break;
			}
			$index ++;
		}
		$this->updateMongo ( array (
				'total' => $total,
				'reday' => 1 
		), array (
				'_id' => $id 
		), 'push', 'century_admin' );
		return $count;
	}
	
	public function del($id){
		$this->removeMongo(array('_id'=>intval($id) ), 'push','century_admin');
		$rediska = new Rediska();
		$list = new Rediska_Key_List('Redis_push_'.$id);
		$list->delete();
		return true;
	}
	public function getRemain($id){
		$rediska = new Rediska();
		$list = new Rediska_Key_List('Redis_push_'.$id);
		return $list->count();
	}
	public function getResect($count=100){
		return $this->getFromMongo(array(), 'push',array('_id'=>-1),0,100,'century_admin');	
	}
	public function getNeedSend(){
		return $this->getOneFromMongo(array('issend'=>false,'reday'=>1,'sendtime'=>array('$lte'=>time())), 'push','century_admin');
	}
	public function hasSend($id,$count=0){
		 $this->updateMongo(array('issend'=>true), array('_id'=>$id), 'push','century_admin',array('sendcount'=>$count));
	}
	
	public function insertAllUidInRedis($id,$index){
		$ret=$this->getFromMongo(array('channel'=>'ANDROID','push_error'=>array('$exists'=>false)), 'users',array('time'-1),$index*$this->onesCount,$this->onesCount,'centurywar');
		$rediska = new Rediska();
		$list = new Rediska_Key_List('Redis_push_'.$id);
		foreach ($ret as $key=>$value){
			$list[]=$value['uid'];
		}
		return count($ret);
	}
	/**
	 * 标记UID有问题，下次不再添加
	 * @param unknown_type $id
	 */
	public function signUidError($uid,$error_code){
		$this->updateMongo(array('push_error'=>$error_code), array('uid'=>$uid), 'users','centurywar');
	}
}
