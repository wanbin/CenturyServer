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
		return $this->add($beahve);
	}
	
}