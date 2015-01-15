<?php
/**
 * @author WanBin @date 2012-12-26
 * 这个类是本地游戏的管理类，本地游戏的缩略图、顺序，是否推荐，进行管理，如果用户明确不喜欢，还可以隐藏
 */
require_once PATH_CACHE . 'LocalCache.php';
class LocalHandler extends LocalCache {
	
	/**
	 * 添加一个新闻公告
	 */
	public function create($name, $gameid, $img) {
		return parent::create($name, $gameid, $img);
	}
	
	public function update($id,$name,$type, $gameid, $img,$des,$sort){
		return parent::update($id, $name,$type, $gameid, $img,$des, $sort);
	}
	
	public function del($id){
		return parent::del($id);
	}
	
	public function getPage($type) {
		$ret = parent::getPage ( $type );
		$typeList = $this->getTypeList ();
		$typeArr = array ();
		foreach ( $typeList as $typevalue ) {
			$typeArr [$typevalue ['value']] = $typevalue ['content'];
		}
		foreach ( $ret as $key => $value ) {
			$ret [$key] ['typename'] = "(".$typeArr [$value ['type']]."游戏）";
		}
		return $ret;
	}
	
	public function getTypeList(){
		return array(
				array('value'=>1,'content'=>'身份类'),
				array('value'=>2,'content'=>'随机类'),
				array('value'=>3,'content'=>'惩罚类'),
				array('value'=>4,'content'=>'互动类'),
		);
	}
	
}