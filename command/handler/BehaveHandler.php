<?php
require_once PATH_CACHE . 'BehaveCache.php';
/**
 * @author wanhin
 * 用户行为统计类
 *
 */
class BehaveHandler extends BehaveCache{
	public function addArray($contentArr) {
		$value ['sec']=array();
		foreach ( $contentArr as $key => $value ) {
			if ($this->checkSec ( $value )) {
				$this->newBehave ( $value ['behave'], $value ['data'], $value ['sec'] );
				$recArray[]=$value ['sec'];
			}
		}
		//把验证通过的sec返回到客户端，让客户端进行删除处理
		return $value ['sec'];
	}
	/**
	 * 检查一下是否密码是否正常
	 */
	public function checkSec($array) {
		return true;
	}
	
	
	/**
	 * 添加一个用户行为
	 */
	public function newBehave($beahve,$data,$sec){
		$content=array(
				'gameuid'=>$this->gameuid,
				'time' => time (),
				'behave' => $beahve,
				'data' => $data,
				'sec'=>$sec,
		);
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
		return parent::getPageShenHe ( $page );
	}
	
	public function addLike($id, $type) {
		if ($type == 1) {
			parent::addLike ( $id, 1, 0 );
		} else {
			parent::addLike ( $id, 0, 1 );
		}
	}
}