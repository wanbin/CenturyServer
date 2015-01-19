<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户建筑类，此类进行操作细化
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_CACHE . 'PunishCache.php';
class PunishHandler extends PunishCache{


	
	/* 
	 * @see PunishCache::newPublish()
	 */
	public function newPublish($message,$type){
		return parent::newPublish($message, $type);
	}
	
	public function updatePunish($id,$content,$contenttype=1){
		return parent::updatePunish($id,$content,$contenttype);
	}
	public function delPunish($id){
		return parent::delPunish($id);
	}
	
	public function like($id,$count){
		return parent::like($id, $count);
	}
	public function dislike($id,$count){
		return parent::dislike($id, $count);
	}
	/**
	 * 审核词汇
	 *
	 * @param unknown_type $id
	 * @param unknown_type $type
	 */
	public function changeShow($id, $type) {
		$ret = $this->getPunish ( $id );
		include_once PATH_HANDLER.'AccountHandler.php';
		$account = new AccountHandler ( $this->uid );
		$typestr="真心话";
		if ($ret ['type'] == "2") {
			$typestr = "大冒险";
		} else if ($ret ['type'] == "3") {
			$typestr = "看演技";
		}
		
		$satus = "已经被审核通过";
		if ($type == 2) {
			$satus = "不符合要求，请适度修改后再提交";
		} else if ($type == 3) {
			$satus = "被管理员删除";
		}
		$punishcount=$ret['content'];
		
		if (strlen ( $punishcount ) > 10) {
// 			$punishcount = substr ( $punishcount, 0, 10 ) . "……";
		}
		
		$mailstr="您提交的$typestr [$punishcount] $satus";
		$account->sendPushByGameuid($ret ['gameuid'], $mailstr,true);
		
		return parent::changeShow ( $id, $type );
	}
	
	public function getRandomOne($type){
		$ret= parent::getRandomOne($type);
		return $ret;
	}
	public function getPunish($punishid){
		return parent::getPunish($punishid);
	}
	

	
	
	public function getPageList($page,$contenttype) {
		$ret = parent::getPage ( $page,1,$contenttype );
		$idarr = array ();
		// 这里取到所有的喜欢不喜欢，进行查询返回
		foreach ( $ret as $key => $valuse ) {
			$idarr [] = $valuse ['_id'];
		}
		
		include_once 'CollectHandler.php';
// 		echo $this->uid;
		$collectHandler = new CollectHandler ( $this->uid );
		$result = $collectHandler->getAllByIds ( $idarr );
		// 取得了所有的喜欢与非喜欢
		$temarray = array ();
		foreach ( $result as $key => $value ) {
			$temarray [$value ['publish_id']] [$value ['type']] = $value ['time'];
		}
		foreach ( $ret as $key => $value ) {
			$ret [$key] ['id'] =$value ['_id'];
			$ret [$key] ['like'] = $result [$value ['_id']]['like'];
			$ret [$key] ['dislike'] =  $result [$value ['_id']]['dislike'];
			$ret [$key] ['liked'] = !empty ( $result [$value ['_id']] ['liked'] );
			$ret [$key] ['disliked'] = !empty ( $result [$value ['_id']] ['disliked'] );
			$ret [$key] ['username']=empty($value['username'])?"匿名":$value['username'];
			$ret [$key] ['type']=$value['type'];
		}
		return $ret;
	}
	
	public function getPageShenHe($page,$showtype) {
		//这一点写的恶心程度令人发指
		$ret= parent::getPage ( $page, $showtype, -1 );
		foreach ($ret as $key=>$value){
			$ret [$key] ['liked'] = false;
			$ret [$key] ['disliked'] = false;
			$ret [$key] ['collected'] = false;
		}
		return $ret;
	}
	
	public function getRepeat(){
		return parent::getRepeat ();
	}
	
	public function addLikeWith($id, $type,$costOther=0) {
			// 这个是标记为喜欢或不喜欢 1，喜欢 2，不喜欢
		include_once 'CollectHandler.php';
		$collectHandler = new CollectHandler ( $this->uid );
		$result = $collectHandler->newCollect ( $id,$type );
		return parent::addLike ( $id, $type);
	}
	
	
	public function getUserLikeList(){
		include_once 'CollectHandler.php';
		$collectHandler = new CollectHandler ( $this->uid );
		$list=$collectHandler->getUserListList();
		$result=array();
		foreach ($list as $key=>$value){
			$result[]=$this->getPunish($key);
		}
		return $result;
		
	}
}