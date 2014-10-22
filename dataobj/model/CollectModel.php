<?php
/**
 * @author WanBin @date 2013-12-30
 * 用户收藏表、点赞
 */
require_once PATH_MODEL.'BaseModel.php';
class CollectModel extends BaseModel {
	
	
	protected function checkCollete($id) {
		$res=$this->oneSqlSignle("select type from user_collect where publish_id=$id and gameuid=".$this->gameuid);
		return $res['type'];
	}
	
	/**
	 *通过ID数组返回数据
	 */
	protected function getAllByIds($idarray) {
		$str = "";
		foreach ( $idarray as $id ) {
			$str .= $id . ",";
		}
// 		$sql = "select * from user_collect where publish_id in($str) and gameuid=" . $this->gameuid;
// 		$str=trim($str,",");
// 		return $this->oneSql ( $sql );
		return array();	
	}
	
	protected function add($id,$type){
		$gameuid=$this->gameuid;
		$time=time();
// 		$sql="insert into user_collect(gameuid,publish_id,type,time) values($gameuid,$id,$type,$time)";
// 		$this->oneSql($sql);
		return ture;
	}
	
	
	/**
	 * 添加喜欢
	 * @param unknown_type $id
	 * @param unknown_type $like
	 * @param unknown_type $dislike
	 * @param unknown_type $collete
	 */
	protected function addLike($id,$type){
		$content=array(
				'gameuid'=>$this->gameuid,
				'punishid'=>$id,
				'type'=>$type
		);
		return $this->insertMongo($content, 'punish_like');
	}
}