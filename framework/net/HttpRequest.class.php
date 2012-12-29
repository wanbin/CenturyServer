<?php

class HttpRequest {
	const USER_AGENT = 'Elex php curl agent 1.0';
	protected static $default_options = array(
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_USERAGENT => self::USER_AGENT,
	CURLOPT_FOLLOWLOCATION => 1,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_HEADER => true);
	/**
	 * 向指定的URL执行一个GET请求
	 * @param $url
	 * @param $headers
	 * @param $time_out
	 * @return array
	 */
	public static function get($url,$headers = null,$time_out = 0){
		return self::sendRequest($url,null,$headers,$time_out);
	}
	/**
	 * 向指定的URL执行一个POST请求
	 * @param $url
	 * @param $post_data
	 * @param $headers
	 * @param $time_out
	 * @return array
	 */
	public static function post($url,$post_data,$headers = null,$time_out = 0){
		return self::sendRequest($url,$post_data,$headers,$time_out);
	}
	
	protected static function sendRequest($url,$post_data = null,$headers = null,$time_out = 0){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt_array($ch,self::$default_options);
	    self::setHeader($ch,$headers);
	    if($post_data){
	    	$post_string = self::createPostString($post_data);
			if(isset($post_string[0])){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			}
	    }
	    if($time_out > 0){
			curl_setopt($ch,CURLOPT_TIMEOUT,$time_out);
	    }
		$data = curl_exec($ch);
	    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    $errno = curl_errno($ch);
	    $error = curl_error($ch);
	    curl_close($ch);
	    if ($errno != CURLE_OK) {
	      return array('errno' => $errno,'errmsg' => $error, 'http_code' => $http_code);
	    }else{
	    	return self::parseResponse($data,$http_code);
	    }
	}
	
	protected static function setHeader($ch,$headers){
		if ($headers && is_array($headers)) {
	    	// Disable Expect: 100-Continue
	    	// http://be2.php.net/manual/en/function.curl-setopt.php#82418
	    	$headers[] = "Expect:";
	    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    }else{
	    	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
	    }
	}
	
	protected static function createPostString($post_data){
		if(empty($post_data) || !is_array($post_data)){
			return $post_data;
		}
		$post_string = '';
		foreach($post_data as $key => $val) {
			if (is_array($val) || is_object($val)) {
				foreach ( $val as $cur_key =>  $cur_val) {
					$post_string .= urlencode($cur_key)."[]=".urlencode($cur_val)."&";
				}
			} else
				$post_string .= urlencode($key)."=".urlencode($val)."&";
		}
		
		return trim($post_string,'&');
	}
	
	protected static function parseResponse($data,$http_code){
		if(empty($data)){
			return array('data' => '','http_code' => $http_code);
		}
		list($raw_response_headers, $response_body) = explode("\r\n\r\n", $data, 2);
		$response_header_lines = explode("\r\n", $raw_response_headers);
		if($response_header_lines[0] == 'HTTP/1.1 100 Continue'){
			list($raw_response_headers, $response_body) = explode("\r\n\r\n", $response_body, 2);
			$response_header_lines = explode("\r\n", $raw_response_headers);
		}
		array_shift($response_header_lines);
		$headers = array();
		foreach($response_header_lines as $header_line){
			list($header, $value) = explode(': ', $header_line, 2);
			if(isset($headers[$header])){
				$headers[$header] .= "\n" . $value;
			}
			else{
				$headers[$header] = $value;
			}
		}
		$response = array('data' => $response_body, 'http_code' => $http_code, 'headers' => $headers);
		return $response;
	}
}

?>