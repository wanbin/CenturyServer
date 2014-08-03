<?php
/**
	More & Original PHP Framwork
	Copyright (c) 2007 - 2008 IsMole Inc.

	描述:开发基于校内开放平台开发第三方应用
	帮助文档: http://dev.xiaonei.com/wiki/%E9%A6%96%E9%A1%B5

	Author:Apptz团队 roger@apptz.com
	$Id: xiaonei.php 404 2008-10-27 07:34:36Z kimi $
*/

class API_Xiaonei {
	  public $secret;
	  public $session_key;
	  public $api_key;
	  public $v;
	  public $server_addr;
	  public $method;

	//note 错误信息返回用
	public $msgMethod;

	public function __construct($api_key,$secret,$v='1.0') {
		$this->secret       = $secret;
		$this->api_key  = $api_key;
		$this->v=$v;
		$this->session_key = MooGetGPC('xn_sig_session_key', 'string');
		
		$this->server_addr='http://api.xiaonei.com/restserver.do';

	}
	
	public function auth($method) {
		$params=array();
		switch($method){
			case 'createToken':
				break;
			
			case 'getSession':
				$authTokenArray=self::auth('createToken');
				
				if($authTokenArray[0]&&$authTokenArray[0][0])
				{
						$authToken=$authTokenArray[0][0];
						$params['authToken']=$authToken;
				}else
				{
					return false;
				}
				
				break;

		}
		

		$method='xiaonei.auth.'.$method;

		return $this->post_request($method,$params);
	}
	


	public function users($method,$array=array(),$format='JSON') {
		$params=array();
		switch($method){
			case 'getInfo':
				if($array['uids'])
				$params['uids']=$array['uids'];
				else $params['uids']=MooGetGPC('xn_sig_user', 'string');
				if($array['fields'])
				$params['fields']=$array['fields'];

				break;
			
			case 'getLoggedInUser':
				break;

			case 'isAppAdded':
				if($array['uid'])
				$params['uid']=$array['uid'];
				break;
		}
		

		$method='xiaonei.users.'.$method;
		$params['format']=$format;

		return $this->post_request($method,$params);
	}
	

	public function profile($method,$array=array(),$format='JSON') {
		$params=array();
		switch($method){
			case 'getXNML':
				if($array['uid'])
				$params['uid']=$array['uid'];
				else $params['uid']=MooGetGPC('xn_sig_user', 'string');break;
			
			case 'setXNML':
				if($array['uid'])
				$params['uid']=$array['uid'];
				if($array['profile'])
				$params['profile']=$array['profile'];
				if($array['profile_action'])
				$params['profile_action']=$array['profile_action'];
				break;

		}
		

		$method='xiaonei.profile.'.$method;
		$params['format']=$format;

		return $this->post_request($method,$params);
	}
	

	public function friends($method,$array=array(),$format='JSON') {
		$params=array();
		switch($method){
			case 'get':
				break;
			case 'getFriends':
				if($array['page'])
				$params['page']=$array['page'];
				if($array['count'])
				$params['count']=$array['count'];break;
			
			case 'areFriends':
				if($array['uids1'])
				$params['uids1']=$array['uids1'];
				if($array['uids2'])
				$params['uids2']=$array['uids2'];
				break;

			case 'getAppUsers':
				break;

		}
		

		$method='xiaonei.friends.'.$method;
		$params['format']=$format;

		return $this->post_request($method,$params);
	}

	public function feed($method,$array=array(),$format='JSON'){
			$params=array();
			switch($method){
					case 'publishTemplatizedAction':
							 if($array['template_id'])
							 		$params['template_id']=$array['template_id'];
							 if($array['title_data'])
							 		$params['title_data']=$array['title_data'];
							 if($array['body_data'])
							 		$params['body_data']=$array['body_data'];
							 if($array['resource_id'])
							 		$params['resource_id']=$array['resource_id'];
							 break;
			}
			$method='xiaonei.feed.'.$method;
			$params['format']=$format;

			return $this->post_request($method,$params);
	}
	
	public function invitations($method,$array=array(),$format='JSON'){
			$params=array();
			switch($method){
					case 'getOsInfo':
							 if($array['invite_ids'])
							 		$params['invite_ids']=$array['invite_ids'];
							 break;
			}
			$method='xiaonei.invitations.'.$method;
			$params['format']=$format;

			return $this->post_request($method,$params);
	}

	public function notifications($method,$array=array(),$format='JSON'){
			$params=array();
			switch($method){
					case 'send':
							 if($array['to_ids'])
							 		$params['to_ids']=$array['to_ids'];
							 if($array['notification'])
							 		$params['notification']=$array['notification'];
							 break;
			}
			$method='xiaonei.notifications.'.$method;
			$params['format']=$format;

			return $this->post_request($method,$params);
	}
	
	
	public function photos($method,$array=array(),$format='JSON') {
		$params=array();
		switch($method){
			case 'getAlbums':
				if($array['uid'])
				$params['uid']=$array['uid'];
				else $params['uid']=MooGetGPC('xn_sig_user', 'string');
				break;
			
			case 'get':
				break;

		}
		

		$method='xiaonei.photos.'.$method;
		$params['format']=$format;

		return $this->post_request($method,$params);
	}
	

	public function messages($method,$array=array(),$format='JSON') {
		$params=array();
		switch($method){
			case 'gets':
				if($array['isInbox'])
				$params['isInbox']=$array['isInbox'];
				else $params['isInbox']=true;
				break;

			case 'get':
				break;

		}


		$method='xiaonei.message.'.$method;
		$params['format']=$format;

		return $this->post_request($method,$params);
	}

	//=================================================================================================
	public static function generate_sig($params_array, $secret) {
			$str = '';

			ksort($params_array);
			// Note: make sure that the signature parameter is not already included in
			// $params_array.
			foreach ($params_array as $k=>$v) {
			$str .= "$k=$v";
			}
			$str .= $secret;

			return md5($str);
		}


	private function create_post_string($method, $params) {
			$params['method'] = $method;
			$params['session_key'] = $this->session_key;
			$params['api_key'] = $this->api_key;
			$params['call_id'] = microtime(true);
			if ($params['call_id'] <= $this->last_call_id) {
			$params['call_id'] = $this->last_call_id + 0.001;
			}
			$this->last_call_id = $params['call_id'];
			if (!isset($params['v'])) {
			$params['v'] = '1.0';
			}
			$post_params = array();
			foreach ($params as $key => &$val) {
			if (is_array($val)) $val = implode(',', $val);
			$post_params[] = $key.'='.urlencode($val);
			}
			$secret = $this->secret;
			$post_params[] = 'sig='.$this->generate_sig($params, $secret);
			return implode('&', $post_params);
		}


	public function post_request($method, $params) {

			//note 返回提示用
			$this->msgMethod = $method;

			$post_string = $this->create_post_string($method, $params);
			//echo '<a href="'.$this->server_addr.'?'.$post_string.'">+</a>';
			if (function_exists('curl_init')) {
			// Use CURL if installed...
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->server_addr);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($ch, CURLOPT_USERAGENT, 'Facebook API PHP5 Client 1.1 (curl) ' . phpversion());
			$result = curl_exec($ch);
			curl_close($ch);
			} else {
			// Non-CURL based version...
			$context =
				array('http' =>
					array('method' => 'POST',
							'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
										'User-Agent: Facebook API PHP5 Client 1.1 (non-curl) '.phpversion()."\r\n".
										'Content-length: ' . strlen($post_string),
							'content' => $post_string));
			$contextid=stream_context_create($context);

			$sock=fopen($this->server_addr, 'r', false, $contextid);
			if ($sock) {
				$result='';
				while (!feof($sock))
				$result.=fgets($sock, 4096);

				fclose($sock);
			}
			}
			$result = $this->xml_to_array($result);
			$this->checkreturn($result);
			return $result;
	}

	private function xml_to_array($xml) {
		$array = (array)(simplexml_load_string($xml));
		foreach ($array as $key=>$item){
			$array[$key]= $this->struct_to_array((array)$item);
		}
		return $array;
	}

	private function struct_to_array($item) {
		if(!is_string($item)) {
			$item = (array)$item;
			foreach ($item as $key=>$val){
			$item[$key]=self::struct_to_array($val);
			}
		}
		return $item;
	}

	private function checkreturn($result) {
		$msg='';
		if($result['error_code'])
		{
			$msg.='<br />访问出错!<br />';
			if($result['error_code'][0])
			{
				$msg.='错误编号:'.$result['error_code'][0].'<br>';
			}
			if($result['error_msg'][0])
			{
				$msg.='错误信息:'.$result['error_msg'][0].'<br>';
			}

		}
		
		if($msg!=''){echo $msg;exit;}

	}
}
