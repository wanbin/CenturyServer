<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: MooUpload.class.php 378 2008-08-01 05:19:08Z lulu $
*/


!defined('IN_MOOPHP') && exit('Access Denied');


/*
* 使用方法：
$upload = MooAutoLoad('MooUpload');
$upload->config(array(
	'targetDir' => './../Moo-data/attachments/',
	'saveType' => 1,
	'thumbStatus' => 1,
	'waterMarkStatus' => 1,
	'imageConfig' => array('thumbDir'=>'./../Moo-data/attachments/thumb/')
));
$files = $upload->saveFiles('upfile');
* upfile 上传文件表单的文件变量名称
* $files 为一个上传成功后的一个文件信息数组
**/

class MooUpload {

	//note 上传的目录，确保可写
	var $targetDir = '';

	//note 上传保存的方式：0为不生成子目录，即全部保存在一个目录下  1 按月方式为一个子目录存储 2 按天方式为一个子目录存储
	var $saveType =1;

	//note 返回的上传信息
	var $upFiles = array();

	//note 图片数组扩展后缀
	var $images = array('jpg', 'jpeg', 'gif', 'png', 'bmp');

	//note 可以上传的文件类型,不带点
	var $allowExtensions = array();

	//note 是否开启缩略图
	var $thumbStatus = 0;

	//note 是否开启水印, 需要在 $imageConfig 同时配置
	var $waterMarkStatus = 0;

	//note 缩略图和水印的参数 数组, 具体配置说明看 MooImage的参数说明
	var $imageConfig = array();

	//note 判断是否已经生成了子目录，防止重复生成
	var $mkSubDirEd = false;

	//note 图片处理类
	var $imageClass = '';

	//note 是否已经配置图片处理类的相应参数
	var $imageConfiged = false;

	/**
	 * 配置函数
	 *
	 * @param array $config: 配置数组,对应的key和变量对应
	 * @return void
	 */
	function config($config) {
		if(is_array($config)) {
			foreach ($config as $var=>$val) {
				if(isset($this->$var)) {
					$this->$var = $val;
				}
			}
		}
	}

	/**
	 * 批量处理上传文件
	 * @param string $upFilename 上传文件的文件变量名称
	 *
	 * @return array $this->upFiles 文件信息数组
	 */
	function saveFiles($upFilename) {

		$files = $this->getFiles($upFilename);
		foreach($files as $file) {
			//note 修正特殊条件下tmp_name变量不是正常字符串，故替换之。
			$file['tmp_name'] = str_replace('\\\\', '\\', $file['tmp_name']);
			if(!is_uploaded_file($file['tmp_name']) || !($file['tmp_name'] != 'none' && $file['tmp_name'] && $file['name'])) {
				continue;
			}
			if(!empty($this->allowExtensions) && !in_array($this->getExtension($file['name']), $this->allowExtensions)) {
				continue;
				//uploadError('Not AllowExtensions Attachment!');
			}
			$this->upFiles[] = $this->saveFile($file);
		}
		return $this->upFiles;
	}

	/**
	 * 保存处理单个文件
	 *
	 * @return string
	 */
	function saveFile(& $file) {
		$upFile = $imageFile = array();
		$this->getSubDir();
		$upFile['path'] = $this->targetDir;
		$upFile['filename'] = $file['name'];
		$upFile['name'] = date("YmdHis").$this->random(10, 1);
		$upFile['extension'] = $this->getExtension($file['name']);
		$upFile['size'] = $file['size'];
		$upFile['isimage'] = 0;
		$imageFile['size'] = $file['size'];
		$destination = $upFile['path'].$upFile['name'].'.'.$upFile['extension'];
		if(move_uploaded_file($file['tmp_name'], $destination)) {

			if(in_array($upFile['extension'], array('jpg', 'jpeg', 'gif', 'png', 'swf', 'bmp')) && function_exists('getimagesize') && !@getimagesize($destination)) {
				unlink($destination);
				//uploadError('Not Expected Attachment!');
			}elseif(in_array($upFile['extension'], array('jpg', 'jpeg', 'gif', 'png', 'bmp'))) {
				if($this->thumbStatus || $this->waterMarkStatus) {

					if(!$this->imageClass) {
						$this->imageClass = MooAutoLoad('MooImage');
					}
					$this->imageClass->image($destination, $imageFile);
					if(!$this->imageConfiged) {
						$this->imageClass->config($this->imageConfig);
						$this->imageConfiged = true;
					}
					$this->imageClass->thumbStatus && $this->imageClass->thumb();
					$this->imageClass->waterMarkStatus && $this->imageClass->Watermark();
					$upFile = array_merge($this->imageClass->upFile, $upFile);
				}
				$upFile['isimage'] = 1;
			}
			return $upFile;
		}
	}


	/**
	 * 根据指定存储的方式取得上传子目录
	 *
	 * @return void
	 */
	function getSubDir() {

		if($this->mkSubDirEd){
			return ;
		}

		$this->mkSubDirEd = true;

		if(empty($this->targetDir)) {
			$this->targetDir = MOOPHP_DATA_DIR.'/attachments/';
		}

		if(!is_dir($this->targetDir)) {
			mkdir($this->targetDir, 0777);
			touch($this->targetDir.'index.htm');
		}

		if($this->saveType == 1) {
			$this->targetDir .= date('Ym'). '/';
		}else if($this->saveType == 2) {
			$this->targetDir .= date('Ymd').'/';
		}else {

		}

		if(!is_dir($this->targetDir)) {
			mkdir($this->targetDir, 0777);
			touch($this->targetDir.'index.htm');
		}
	}

	/**
	 * 返回上传文件不带"."的扩展名
	 *
	 * @return string
	 */
	function getExtension($fileName) {
		return  strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
	}


	/**
	 * 把多个文件上传的信息拆分后返回
	 * @param string $upFilename  表单上传文件变量的名称
	 *
	 * @return array FilesInfo
	 */
	function getFiles($upFilename) {
		$upFiles = array();
		if(isset($_FILES[$upFilename]) && is_array($_FILES[$upFilename])) {
			foreach($_FILES[$upFilename] as $key => $var) {
				if(!is_array($var)) {
					$upFiles[0] = $_FILES[$upFilename];
					break;
				}
				foreach($var as $id => $val) {
					$upFiles[$id][$key] = $val;
				}
			}
		}
		return $upFiles;
	}

	/**
	 * 返回随机字符
	 * @param int $length  字符长度
	 * @param boolean $numeric  是否是数字
	 *
	 * @return string random string
	 */
	function random($length, $numeric = 0) {
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		if($numeric) {
			$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
		} else {
			$hash = '';
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++) {
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		return $hash;
	}

}
