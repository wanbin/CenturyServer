<?php
import('elex.net.HttpRequest');

class HttpMultiRequest extends HttpRequest {
	protected $multi_handle = null;
	protected $handles = array();
	
	protected function addRequest($url,$post_data,$header = null,$method = 'POST',$timeout = 0){
		if($this->multi_handle === null){
			$this->multi_handle = curl_multi_init();
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		if($post_data){
			$post_string = $this->createPostString($post_data);
			if(isset($post_string[0])){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			}
//			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}
		curl_setopt_array($ch,self::$default_options);
		if($timeout > 0){
			curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
		}
		$this->setHeader($ch,$header);
		curl_multi_add_handle($this->multi_handle,$ch);
		$this->handles[$url] = $ch;
	}

	/**
	 * 添加一个POST方式的请求
	 * @param $url
	 * @param $post_data
	 * @param $header
	 * @param $timeout
	 * @return void
	 */
	public function addPostRequest($url,$post_data,$header = null,$timeout = 60){
//		if(empty($header)){
//			$header = array('Content-Type: multipart/form-data');
//		}
		$this->addRequest($url,$post_data,$header,$timeout);
	}
	/**
	 * 添加一个GET方式的HTTP请求
	 * @param $url
	 * @param $header
	 * @param $timeout
	 * @return void
	 */
	public function addGetRequest($url,$header = null,$timeout = 60){
		$this->addRequest($url,null,$header,'GET',$timeout);
	}
	/**
	 * 并行执行所有添加的请求，并返回结果。结果是以url作为key的关联数组。
	 * @return array
	 */
	public function exec(){
		// Start performing the request
		$runningHandles = 0;
		$ret = 0;
		do{
			$ret = curl_multi_exec($this->multi_handle, $runningHandles);
		}while($ret == CURLM_CALL_MULTI_PERFORM);
		// Loop and continue processing the request
		while($runningHandles && $ret == CURLM_OK){
			// Wait forever for network
			$numberReady = curl_multi_select($this->multi_handle);
			if($numberReady != - 1){
				// Pull in any new data, or at least handle timeouts
				do{
					$ret = curl_multi_exec($this->multi_handle, $runningHandles);
				}while($ret == CURLM_CALL_MULTI_PERFORM);
			}
		}
		
		// Check for any errors
		if($ret != CURLM_OK){
			return array('errno' => $ret,'errmsg' => 'Curl multi read error');
		}
		$result = array();
		// Extract the content
		foreach($this->handles as $url => $ch){
			$result[$url] = $this->getResponse($ch);
			// Remove and close the handle
			curl_multi_remove_handle($this->multi_handle, $ch);
			curl_close($ch);
		}
		$this->handles = array();
		// Clean up the curl_multi handle
		curl_multi_close($this->multi_handle);
		$this->multi_handle = null;
		return $result;
	}
	
	protected static function getResponse($ch){
		$response = array();
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		// Check for errors
		$errno = curl_errno($ch);
		if($errno == CURLE_OK){
			$data = curl_multi_getcontent($ch);
			$response = self::parseResponse($data,$http_code);
		}
		else{
			$err_msg = curl_error($ch);
			$response = array('http_code' => $http_code, 'errno' => $errno, 'errmsg' => $err_msg);
		}
		return $response;
	}
}
