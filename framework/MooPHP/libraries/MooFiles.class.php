<?php
/*
	more & original php framwork
	copyright (c) 2007 - 2008 ismole inc.

	$Id: MooFiles.class.php 273 2008-06-02 00:23:07Z aming $
*/

!defined('IN_MOOPHP') && exit('Access Denied');

class MooFiles {
	var $dirList = array();

	/**
	 * 读取文件
	 *
	 * @param boolean $exit
	 * @param string $file
	 * @return string
	 */
	function fileRead($file, $exit = TRUE) {
		return MooReadFile($file, $exit);
	}

	/**
	 * 存储数据
	 * 
	 * @param string $file
	 * @param string $content
	 * @param string $mod
	 * @param boolean $exit
	 * @return boolean
	 */
	function fileWrite($file, $content, $mod = 'w', $exit = TRUE) {
		return MooWriteFile($file, $content, $mod, $exit);
	}

	/**
	 * 删除操作
	 *
	 * @param string $file：
	 * @return boolean;
	 */
	 function fileDelete($folder) {
		if(is_file($folder) && file_exists($folder)) {
			unlink($folder);
		}
		if(is_dir($folder)) {
			$handle = opendir($folder);
			while(false !== ($myFile = readdir($handle))) {
				if($myFile != '.' && $myFile != '..') {
					$this->fileDelete($folder.'/'.$myFile);
				}
			}
			closedir($handle);
			rmdir($folder);
		}
		unset($folder);
		return true;
	}

	/**
	 * 创建文件或目录
	 *
	 * @param string $file：
	 * @param string $type：
	 * @return boolean;
	 */
	function fileMake($file, $type = 'dir') {
		$array = explode('/', $file);
		$count = count($array);
		$msg = '';
		if($type == 'dir') {
			for($i = 0; $i < $count; $i++) {
				$msg .= $array[$i];
				if(!file_exists($msg) && ($array[$i])) {
					mkdir($msg, 0777);
				}
				$msg .= '/';
			}
		} else {
			for($i = 0; $i < ($count-1); $i++) {
				$msg .= $array[$i];
				if(!file_exists($msg) && ($array[$i])) {
					mkdir($msg, 0777);
				}
				$msg .= '/';
			}
			global $systemTime;
			$theTime = $systemTime ? $systemTime : time();
			//note:创建文件
			@touch($file, $theTime);
			unset($theTime);
		}
		unset($msg, $file, $type, $count, $array);
		return true;
	}


	/**
	 * 复制操作
	 *
	 * @param string $old：
	 * @param string $new：
	 * @param boolean $recover：
	 * @return boolean;
	 */
	function fileCopy($old, $new, $recover = true) {
		if(substr($new, -1) == '/') {
			$this->fileMake($new, 'dir');
		} else {
			$this->fileMake($new, 'file');
		}
		if(is_file($new)) {
			if($recover) {
				unlink($new);
			} else {
				return false;
			}
		} else {
			$new = $new.basename($old);
		}
		copy($old, $new);
		unset($old, $new, $recover);
		return true;
	}


	/**
	 * 文件移动操作
	 *
	 * @param string $old：
	 * @param string $new：
	 * @param boolean $recover：
	 * @return boolean;
	 */
	function fileMove($old, $new, $recover = true) {
		if(substr($new, -1) == '/') {
			$this->fileMake($new, 'dir');
		} else {
			$this->fileMake($new, 'file');
		}
		if(is_file($new)) {
			if($recover) {
				unlink($new);
			} else {
				return false;
			}
		} else {
			$new = $new.basename($old);
		}
		rename($old, $new);
		unset($old, $new, $recover);
		return true;
	}

	/**
	 * 获取文件夹列表
	 *
	 * @param string $folder：
	 * @param boolean $isSubDir：
	 * @return array;
	 */
	function getDirList($folder, $isSubDir = false) {
		$this->dirList = array();
		if(is_dir($folder)) {
			$handle = opendir($folder);
			while(false !== ($myFile = readdir($handle))) {
				if($myFile != '.' && $myFile != '..') {
					$this->dirList[] = $myFile;
					if($isSubDir && is_dir($folder.'/'.$myFile)) {
						$this->getDirList($folder.'/'.$myFile, $isSubDir);
					}
				}
			}
			closedir($handle);
			unset($folder, $isSubDir);
			return $this->dirList;
		}
		return $this->dirList;
	}

	/**
	 * 打开文件
	 *
	 * @param string $file：
	 * @param string $type：
	 * @return resource;
	 */
	function fileOpen($file, $type = 'wb') {
		$handle = fopen($file, $type);
		return $handle;
	}
	
	/**
	 * 关闭指针
	 *
	 * @param resource $handle
	 * @return boolean
	 */
	function fileClose($handle) {
		return fclose($handle);
	}
}