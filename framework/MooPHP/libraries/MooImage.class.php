<?php
/*
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	$Id: MooImage.class.php 405 2008-11-26 02:25:35Z kimi $
*/

!defined('IN_MOOPHP') && exit('Access Denied');

/**
 * 使用方法
$image = MooAutoLoad('MooImage');
//各个参数的意义可以看下面对应的说明
$image->config(array('waterMarkMinWidth' => '400', 'waterMarkMinHeight' => '300', 'waterMarkStatus' => 9));
$image->thumb(200, 260, './../Moo-data/attachments/2.jpg');
$image->waterMark();
 */


class MooImage {

	//note 需要处理的源文件
	var $targetFile = '';

	//note 需要处理的源文件的信息
	var $targetInfo = '';

	//note 处理的函数
	var $imageCreateFromFunc = '';
	var $imageFunc = '';

	//note 返回的处理信息数组
	var $upFile = array();

	//note 动态的gif图片不处理
	var $animatedGif = 0;

	//note 缩略图开关 0为关闭 1为生成指定大小的缩略图 2把原图缩放为指定大小的缩略图
	var $thumbStatus = 1;

	//note 指定缩略图的宽
	var $thumbWidth = 400;

	//note 指定缩略图的高
	var $thumbHeight = 300;

	//note  jpeg 类型文件缩略图质量参数，范围为 0～100 的整数，数值越大结果图片效果越好
	var $thumbQuality = 100;

	//note 缩略图保存路径 为空的时候和源文件同目录， 指定的时候末尾需要加 /
	var $thumbDir = '';

	//note 缩略图的方式：0为不生成子目录，即全部保存在一个目录下  1 按月方式为一个子目录存储 2 按天方式为一个子目录存储
	var $saveType = 1;

	//note 缩略图名称: 空就在源文件名后加上 .thumb.jpg,  random 就是随机生成， 其他的就按照指定名称
	var $thumbName = '';

	//note 0 关闭水印 1左上 2中上 3右上 4中左  5正中  6中右 7左下 8中下 9右下
	var $waterMarkStatus = 0;

	//note 0 gif类型的文件作为水印 1 png类型的文件作为水印
	var $waterMarkType = 0;

	//note 水印文件路径
	var $waterImagePath = './../Moo-data/images/';

	//note 添加水印的条件， 源图片的最小宽度
	var $waterMarkMinWidth = 0;

	//note 添加水印的条件，源图片的最小高度
	var $waterMarkMinHeight = 0;

	//note 水印融合度, 1～100 的整数，数值越大水印图片透明度越低
	var $waterMarkTrans = 65;

	//note jpeg 类型的图片添加水印后质量参数，范围为 0～100 的整数，数值越大结果图片效果越好
	var $waterMarkQuality = 100;

	//note 判断是否已经生成了子目录，防止重复生成
	var $mkSubDirEd = false;

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
	 * 初始化函数
	 *
	 * @param string $targetFile: 需要处理的目标图片文件地址
	 * @param array $upFile: 目标图片的一些文件信息
	 * @return void
	 */
	function image($targetFile, $upFile = array()) {
		$this->targetFile = $targetFile;
		$this->upFile = $upFile;
		$this->targetInfo = @getimagesize($targetFile);
		switch($this->targetInfo['mime']) {
			case 'image/jpeg':
				$this->imageCreateFromFunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
				$this->imageFunc = function_exists('imagejpeg') ? 'imagejpeg' : '';
				break;
			case 'image/gif':
				$this->imageCreateFromFunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
				$this->imageFunc = function_exists('imagegif') ? 'imagegif' : '';
				break;
			case 'image/png':
				$this->imageCreateFromFunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
				$this->imageFunc = function_exists('imagepng') ? 'imagepng' : '';
				break;
		}

		$this->upFile['size'] = empty($this->upFile['size']) ? @filesize($targetFile) : $this->upFile['size'];
		if($this->targetInfo['mime'] == 'image/gif') {
			$fp = fopen($targetFile, 'rb');
			$targetFileContent = fread($fp, $this->upFile['size']);
			fclose($fp);
			$this->animatedGif = strpos($targetFileContent, 'NETSCAPE2.0') === FALSE ? 0 : 1;
		}
	}

	/**
	 * 根据指定大小生成缩略图（使用GD库）
	 *
	 * @param int $thumbWidth: 指定缩略图的宽
	 * @param int $thumbHeight: 指定缩略图的高
	 * @return void
	 */
	function thumb($thumbWidth = '', $thumbHeight = '', $targetFile = '', $upFile = '') {

		if($targetFile) {
			$this->image($targetFile);
		}

		$thumbWidth = $thumbWidth ? $thumbWidth : $this->thumbWidth;
		$thumbHeight = $thumbHeight ? $thumbHeight : $this->thumbHeight;

		if($this->thumbStatus && function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled') && function_exists('imagejpeg') && $this->imageCreateFromFunc && $this->imageFunc) {
			$imageCreateFromFunc = $this->imageCreateFromFunc;
			$imageFunc = $this->thumbStatus == 1 ? 'imagejpeg' : $this->imageFunc;
			list($imgWidth, $imgHeight) = $this->targetInfo;

			if(!$this->animatedGif && ($imgWidth >= $thumbWidth || $imgHeight >= $thumbHeight)) {

				$upFilePhoto = $imageCreateFromFunc($this->targetFile);

				$XRation = $thumbWidth / $imgWidth;
				$YRation = $thumbHeight / $imgHeight;

				if(($XRation * $imgHeight) < $thumbHeight) {
					$thumb['height'] = ceil($XRation * $imgHeight);
					$thumb['width'] = $thumbWidth;
				} else {
					$thumb['width'] = ceil($YRation * $imgWidth);
					$thumb['height'] = $thumbHeight;
				}

				$this->thumbDir && $this->getSubDir();
				$thumbDir = $this->thumbDir ? $this->thumbDir : pathinfo($this->targetFile, PATHINFO_DIRNAME).'/';
				$thumbName = $this->thumbName ? ($this->thumbName == 'random' ? date("YmdHis").$this->random(10, 1).'.jpg' : $this->thumbName) : basename($this->targetFile).'.thumb.jpg';
				$targetFile = $this->thumbStatus == 1 ? $thumbDir.$thumbName : $this->targetFile;
				$thumbPhoto = imagecreatetruecolor($thumb['width'], $thumb['height']);
				imageCopyreSampled($thumbPhoto, $upFilePhoto ,0, 0, 0, 0, $thumb['width'], $thumb['height'], $imgWidth, $imgHeight);
				clearstatcache();
				if($this->targetInfo['mime'] == 'image/jpeg') {
					$imageFunc($thumbPhoto, $targetFile, $this->thumbQuality);
				} else {
					$imageFunc($thumbPhoto, $targetFile);
				}
				$this->upFile['thumbDir'] = str_replace(MOOPHP_ROOT.'/', '', $thumbDir);
				$this->upFile['thumbName'] = $thumbName;
				$this->upFile['thumbWidth'] = $thumb['width'];
				$this->upFile['thumbHeight'] = $thumb['height'];
				$this->upFile['thumb'] = $this->thumbStatus == 1 ? 1 : 0;
			}
		}else {
			return ;
		}

		if($this->thumbStatus == 2 && $this->waterMarkStatus) {
			$this->image($this->targetFile, $this->upFile);
			$this->upFile['thumb'] = 2;
			$this->upFile['thumbDir'] = pathinfo($this->targetFile, PATHINFO_DIRNAME).'/';
			$this->upFile['thumbName'] = basename($this->targetFile);
		}
		$this->upFile['size'] = filesize($this->targetFile);
	}

	/**
	 * 给图片增加水印（使用GD库）
	 * @return void
	 */
	function waterMark($targetFile = '' , $upFile = '') {

		if($targetFile) {
			$this->image($targetFile, $upFile);
		}

		if(($this->waterMarkMinWidth && $this->targetInfo[0] <= $this->waterMarkMinWidth && $this->waterMarkMinHeight && $this->targetInfo[1] <= $this->waterMarkMinHeight) || ($this->waterMarkType == 2 && (!file_exists($this->waterMarkText['fontpath']) || !is_file($this->waterMarkText['fontpath'])))) {
			return;
		}

		if($this->waterMarkStatus && function_exists('imagecopy') && function_exists('imagealphablending') && function_exists('imagecopymerge')) {
			$imageCreateFromFunc = $this->imageCreateFromFunc;
			$imageFunc = $this->imageFunc;
			list($imgWidth, $imgHeight) = $this->targetInfo;
			if($this->waterMarkType < 2) {
				$waterMarkFile = $this->waterMarkType == 1 ? MOOPHP_ROOT.'/'.$this->waterImagePath .'watermark.png' : MOOPHP_ROOT.'/'.$this->waterImagePath.'watermark.gif';

				$waterMarkInfo = @getimagesize($waterMarkFile);
				$waterMarkLogo = $this->waterMarkType == 1 ? @imageCreateFromPNG($waterMarkFile) : @imageCreateFromGIF($waterMarkFile);
				if(!$waterMarkLogo) {
					return;
				}
				list($logoWidth, $logoHeight) = $waterMarkInfo;
			}
			$wmwidth = $imgWidth - $logoWidth;
			$wmheight = $imgHeight - $logoHeight;

			if(($this->waterMarkType < 2 && is_readable($waterMarkFile) || $this->waterMarkType == 2) && $wmwidth > 10 && $wmheight > 10 && !$this->animatedGif) {
				switch($this->waterMarkStatus) {
					case 1:
						$x = +5;
						$y = +5;
						break;
					case 2:
						$x = ($imgWidth - $logoWidth) / 2;
						$y = +5;
						break;
					case 3:
						$x = $imgWidth - $logoWidth - 5;
						$y = +5;
						break;
					case 4:
						$x = +5;
						$y = ($imgHeight - $logoHeight) / 2;
						break;
					case 5:
						$x = ($imgWidth - $logoWidth) / 2;
						$y = ($imgHeight - $logoHeight) / 2;
						break;
					case 6:
						$x = $imgWidth - $logoWidth;
						$y = ($imgHeight - $logoHeight) / 2;
						break;
					case 7:
						$x = +5;
						$y = $imgHeight - $logoHeight - 5;
						break;
					case 8:
						$x = ($imgWidth - $logoWidth) / 2;
						$y = $imgHeight - $logoHeight - 5;
						break;
					case 9:
						$x = $imgWidth - $logoWidth - 5;
						$y = $imgHeight - $logoHeight - 5;
						break;
				}

				$destinationPhoto = imagecreatetruecolor($imgWidth, $imgHeight);
				$targetPhoto = @$imageCreateFromFunc($this->targetFile);
				imageCopy($destinationPhoto, $targetPhoto, 0, 0, 0, 0, $imgWidth, $imgHeight);

				if($this->waterMarkType == 1) {
					imageCopy($destinationPhoto, $waterMarkLogo, $x, $y, 0, 0, $logoWidth, $logoHeight);
				} else {
					imageAlphaBlending($waterMarkLogo, true);
					imageCopyMerge($destinationPhoto, $waterMarkLogo, $x, $y, 0, 0, $logoWidth, $logoHeight, $this->waterMarkTrans);
				}

				clearstatcache();
				if($this->targetInfo['mime'] == 'image/jpeg') {
					$imageFunc($destinationPhoto, $this->targetFile, $this->waterMarkQuality);
				} else {
					$imageFunc($destinationPhoto, $this->targetFile);
				}

				$this->upFile['size'] = filesize($this->targetFile);
			}
		}
	}

	/**
	 * 根据指定存储的方式取得生成缩略图子目录
	 *
	 * @return void
	 */
	function getSubDir() {

		if(empty($this->thumbDir)) {
			return ;
		}

		if($this->mkSubDirEd){
			return ;
		}

		$this->mkSubDirEd = true;

		if(!is_dir($this->thumbDir)) {
			mkdir($this->thumbDir, 0777);
			touch($this->thumbDir.'index.htm');
		}

		if($this->saveType == 1) {
			$this->thumbDir .= date('Ym').'/';
		}else if($this->saveType == 2) {
			$this->thumbDir .= date('Ymd').'/';
		}else {

		}

		if(!is_dir($this->thumbDir)) {
			mkdir($this->thumbDir, 0777);
			touch($this->thumbDir.'index.htm');
		}
	}

	/**
	 * 返回随机字符
	 * @param int $length  字符长度
	 * @param boolean $numeric  是否是数字
	 *
	 * @return string
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
