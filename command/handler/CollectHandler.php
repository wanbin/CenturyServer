<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户建筑类，此类进行操作细化
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_CACHE . 'CollectCache.php';
/**
 * @author wanhin
 *
 */
class CollectHandler extends CollectCache{
	
	/**
	 * 喜欢游戏
	 * @param unknown_type $id
	 */
	public function like($id){
		$ret=parent::like($id);
		$count=$ret['count'];
		$hasDo=$ret['hasdo'];
		if (in_array($count, array(1,2,3,5,10,20,50,100))&&!$hasDo){
			require_once PATH_HANDLER.'PunishHandler.php';
			$punish=new PunishHandler($this->uid);
			$punishone=$punish->getPunish($id);
			if(!empty($punishone)){
				$temgameuid=$punishone['gameuid'];
				$temcontent=$punishone['content'];
				$temtype=$punish->getTypeName($punishone['contenttype']);
				require_once PATH_HANDLER.'AccountHandler.php';
				$account=new AccountHandler($this->uid);
				$content="您提交的".$temtype."【".$temcontent."】获得了".$count."个称赞！";
				$account->sendPushByGameuid($temgameuid, $content,true);
			}
		}
		return $count;
	}
	/**
	 * 不喜欢游戏
	 * @param unknown_type $id
	 */
	public function dislike($id){
		return parent::dislike($id);
	}
	
	
	/**
	 *通过id数组返回相应的状态
	 */
	public function getAllByIds($idarray){
		return parent::getAllByIds($idarray);
	}
	public function getUserListList(){
		return parent::getUserListList();
	}
}