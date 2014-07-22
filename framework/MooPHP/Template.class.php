<?php
/**
 * 编译模板文件，并且返回模板文件的路径
 * @param $file
 * @return string
 */
function renderTemplate($file){
	if(defined('TEMPLATE_EXT')){
		$ext = TEMPLATE_EXT;
	}
	else{
		$ext = ".htm";
	}
	if(!defined('TEMPLATE_DIR')){
		define('TEMPLATE_DIR','./view');
	}
	if(!defined('TEMPLATE_DATA_DIR')){
		define('TEMPLATE_DATA_DIR','./cache/templates');
	}
	$tplfile = TEMPLATE_DIR.'/'.$file.$ext;
	if(!file_exists($tplfile)){
		die("template file $tplfile not exists");
	}
	$objfile = TEMPLATE_DATA_DIR.'/'.$file.'.tpl.php';
	if(!file_exists($objfile) || filemtime($tplfile) > filemtime($objfile)) {
		if(!file_exists(TEMPLATE_DATA_DIR)){
			mkdir(TEMPLATE_DATA_DIR,0777,true);
		}
		if(!file_exists(dirname($objfile))){
			mkdir(dirname($objfile),0777,true);
		}
		$t = new Template();
		if($t->complie($tplfile,$objfile) === false){
			die('Cannot write to template cache.');
		}
	}
	return $objfile;
}
/**
 * 返回模板文件的内容
 * @param $file
 * @return string
 */
function fetch_template($file){
	ob_start();
	ob_implicit_flush(false);
	include renderTemplate($file);
	return ob_get_clean();
}

function get_tpl_message($resoure,$key){
	static $msgs = array();
	if(empty($resoure) || empty($key)){
		trigger_error('resource or message key empty',E_USER_ERROR);
		return '';
	}
	//$resoure = preg_replace('/[^A-Z0-9_\.-]/i', '',$resoure);
	if(isset($msgs[$resoure])){
		return $msgs[$resoure][$key];
	}
	if(!defined(TEMPLATE_MESSAGE_DIR)){
		define('TEMPLATE_MESSAGE_DIR','./messages');
	}
	if(!defined('LOCALE')){
		define('LOCALE','zh-cn');
	}
	$file = TEMPLATE_MESSAGE_DIR . '/' . LOCALE . '/' . $resoure . '.inc.php';
	if(file_exists($file)){
		$msgs[$resoure] = include $file;
		return $msgs[$resoure][$key];
	}else{
		trigger_error("resource file $file not exist",E_USER_ERROR);
	}
	return '';
}
/**
 * 模板类
 *
 */
class Template {
	protected $var_regexp = "\@?\\\$[a-zA-Z_][\\\$\w]*(?:\-\>[_\\\$\w]+)*(?:\[[\w\-\.\"\'\[\]\$]+\])*";
	protected $vtag_regexp = "\<\?php echo (\@?\\\$[a-zA-Z_][\\\$\w]*(?:\-\>[_\\\$\w]+)*(?:\[[\w\-\.\"\'\[\]\$]+\])*)\;\?\>";
	protected $const_regexp = "\{([\w]+)\}";
	protected $header = "<?php if(!defined('FRAMEWORK')) exit('Access Denied');?>\r\n";
	
	/**
	 *  读模板页进行替换后写入到cache页里
	 *
	 * @param string $tplfile ：模板源文件地址
	 * @param string $objfile ：模板cache文件地址
	 * @return string
	 */
	public function complie($tplfile, $objfile) {
		$template = file_get_contents ( $tplfile );
		$template = $this->parse ( $template );
		return file_put_contents($objfile,$template,LOCK_EX);
	}
	
	/**
	 *  解析模板标签
	 *
	 * @param string $template ：模板源文件内容
	 * @return string
	 */
	protected function parse($template) {
		
		$template = preg_replace ( '/\<\!\-\-\{(.+?)\}\-\-\>/s', "{\\1}", $template ); //去除html注释符号<!---->
		$template = preg_replace ( "/\{($this->var_regexp)\}/", "<?php echo \\1;?>", $template ); //替换带{}的变量
		$template = preg_replace ( "/\{($this->const_regexp)\}/", "<?php echo \\1;?>", $template ); //替换带{}的常量
		$template = preg_replace ( "/(?<!\<\?php echo |\\\\)$this->var_regexp/", "<?php echo \\0;?>", $template ); //替换重复的<?php echo
		$template = preg_replace ( '/\{php (.*?)\}/ies', "\$this->stripvTag('<?php \\1?>')", $template ); //替换php标签
		$template = preg_replace ( '/\{for (.*?)\}/ies', "\$this->stripvTag('<?php for(\\1) {?>')", $template ); //替换for标签
		$template = preg_replace ( '/\{elseif\s+(.+?)\}/ies', "\$this->stripvTag('<?php } elseif (\\1) { ?>')", $template ); //替换elseif标签
		for($i = 0; $i < 3; $i ++) {
			$template = preg_replace ( "/\{loop\s+$this->vtag_regexp\s+$this->vtag_regexp\s+$this->vtag_regexp\}(.+?)\{\/loop\}/ies", "\$this->loopSection('\\1', '\\2', '\\3', '\\4')", $template );
			$template = preg_replace ( "/\{loop\s+$this->vtag_regexp\s+$this->vtag_regexp\}(.+?)\{\/loop\}/ies", "\$this->loopSection('\\1', '', '\\2', '\\3')", $template );
		}
		$template = preg_replace ( '/\{if\s+(.+?)\}/ies', "\$this->stripvTag('<?php if(\\1) { ?>')", $template ); //替换if标签
		$template = preg_replace ( '/\{include\s+(.*?)\}/is', "<?php include \\1; ?>", $template ); //替换include标签
		$template = preg_replace ( '/\{template\s+([\w\.\/\\\\]+?)\}/is', "<?php include renderTemplate('\\1'); ?>", $template ); //替换template标签
		$template = preg_replace ( '/\{(?:msg|%)\s+([\w]+?)\s+([\w]+?)\}/is', "<?php echo get_tpl_message('\\1','\\2'); ?>", $template ); //替换msg标签
		$template = preg_replace ( '/\{else\}/is', '<?php } else { ?>', $template ); //替换else标签
		$template = preg_replace ( '/\{\/(if|for)\}/is', '<?php } ?>', $template ); //替换/if, /for标签
		$template = preg_replace ( "/$this->const_regexp/", "<?php echo \\1;?>", $template ); //note {else} 也符合常量格式，此处要注意先后顺??
		$template = preg_replace ( '/(\$[a-zA-Z_]\w+\[)([a-zA-Z_]\w+)\]/i', "\\1'\\2']", $template ); //将二维数组替换成带单引号的标准模式
		if(isset($this->header[0])){
			$template = $this->header . $template;
		}
		return $template;
	}
	/**
	 * 设置template文件的前置代码
	 * @param string $header
	 */
	public function setHeader($header = ''){
		$this->header = $header;
	}
	/**
	 * 正则表达式匹配替换
	 *
	 * @param string $s ：
	 * @return string
	 */
	protected function stripvTag($s) {
		return preg_replace ( "/$this->vtag_regexp/is", "\\1", str_replace ( "\\\"", '"', $s ) );
	}
	
	protected function stripTagQuotes($expr) {
		$expr = preg_replace ( "/\<\?php echo (\\\$.+?);\?\>/s", "{\\1}", $expr );
		$expr = str_replace ( "\\\"", "\"", preg_replace ( "/\[\'([a-zA-Z0-9_\-\.\x7f-\xff]+)\'\]/s", "[\\1]", $expr ) );
		return $expr;
	}
	
	/**
	 * 替换模板中的LOOP循环
	 *
	 * @param string $arr ：
	 * @param string $k ：
	 * @param string $v ：
	 * @param string $statement ：
	 * @return string
	 */
	protected function loopSection($arr, $k, $v, $statement) {
		$arr = $this->stripvTag ( $arr );
		$k = $this->stripvTag ( $k );
		$v = $this->stripvTag ( $v );
		$statement = str_replace ( "\\\"", '"', $statement );
		return $k ? "<?php foreach($arr as $k=>$v) {?>$statement<?php }?>" : "<?php foreach($arr as $v) {?>$statement<?php } ?>";
	}
}

?>