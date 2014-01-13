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
		$content=array(
				'content'=>$message,
				'time' => time (),
				'type' => $type,
				'isshow' => 0
		);
		//如果是测试，则直接显示出来
		if (TEST) {
			$content ['isshow'] = 1;
		}
		$this->add($content);
	}
	/**
	 * 审核词汇
	 * @param unknown_type $id
	 * @param unknown_type $type
	 */
	public function changeShow($id,$type){
		return parent::changeShow ( $id,$type );
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
			$ret [$key] ['type']=rand(1,6);
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
	
	public function addLike($id, $type) {
		if ($type == 1) {
			parent::addLike ( $id, 1, 0 );
		} else {
			parent::addLike ( $id, 0, 1 );
		}
	}
}