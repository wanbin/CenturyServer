<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户建筑类，此类进行操作细化
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_CACHE . 'PublishCache.php';
class PublishHandler extends PublishCache{
	
	/**
	 * 添加一个新闻公告
	 */
	public function newPublish($message,$type){
		return $this->add($message,$type);
	}
	/**
	 * 审核词汇
	 *
	 * @param unknown_type $id
	 * @param unknown_type $type
	 */
	public function changeShow($id, $type) {
		$ret = $this->getOne ( $id );
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
		
		$mailstr="您提交的$typestr [".$ret['content']."] $satus";
		$account->sendPushByGameuid($ret ['gameuid'], $mailstr,true);
		
		return parent::changeShow ( $id, $type );
	}
	
	public function getRandomOne($type){
		$ret= parent::getRandomOne($type);
		return $ret;
	}
	
	public function getPage($page) {
		$ret = parent::getPage ( $page );
		$idarr = array ();
		// 这里取到所有的喜欢不喜欢，进行查询返回
		foreach ( $ret as $key => $valuse ) {
			$idarr [] = $valuse ['id'];
		}
		
		include_once 'CollectHandler.php';
		$collectHandler = new CollectHandler ( $this->uid );
		$result = $collectHandler->getAllByIds ( $idarr );
		// 取得了所有的喜欢与非喜欢
		$temarray = array ();
		foreach ( $result as $key => $value ) {
			$temarray [$value ['publish_id']] [$value ['type']] = $value ['time'];
		}
		foreach ( $ret as $key => $value ) {
			$ret [$key] ['liked'] = !empty ( $temarray [$value ['id']] [1] );
			$ret [$key] ['disliked'] = !empty ( $temarray [$value ['id']] [2] );
			$ret [$key] ['collected'] = !empty ( $temarray [$value ['id']] [3] );
			$ret [$key] ['username']=empty($value['username'])?"匿名":$value['username'];
			$ret [$key] ['type']=$value['type'];
		}
		return $ret;
	}
	
	public function getPageShenHe($page) {
		$ret= parent::getPageShenHe ( $page );
		foreach ($ret as $key=>$value){
			$ret [$key] ['liked'] = false;
			$ret [$key] ['disliked'] = false;
			$ret [$key] ['collected'] = false;
		}
		return $ret;
	}
	public function addLikeWith($id, $type,$costOther=0) {
			// 这个是标记为喜欢或不喜欢 1，喜欢 2，不喜欢
		if ($type == 1) {
			parent::addLike ( $id, 1, $costOther );
		} else if ($type == 2) {
			parent::addLike ( $id, $costOther, 1 );
		}
	}
}