<?php
/**
 * 输出amf格式的内容
 * @param $result 需要输出到浏览器的内容
 * @param $exit 是否在输出后结束脚本
 * @return void
 */
function print_amf_result($result, $exit = true) {
	if($GLOBALS['amfphp']['native'] === true && function_exists('amf_decode')){
		$serializer = new AMFBaseSerializer(); // Create a serailizer around the output stream
	}
	else{
		$serializer = new AMFSerializer(); // Create a serailizer around the output stream
	}
	$body = new MessageBody('', '/1');
	$body->responseURI = $body->responseIndex . "/onResult";
	$body->setResults($result);
	$amfObj = new AMFObject();
	$amfObj->addBody($body);
	$data = $serializer->serialize($amfObj);
	header('Content-type: application/x-amf');
	$dateStr = date('D, j M Y H:i:s', time() - 86400);
	header("Expires: $dateStr GMT");
	header('Pragma: no-store');
	header('Cache-Control: no-store');
	header('Content-length: ' . strlen($data));
	echo $data;
	if($exit){
		exit();
	}
}
/**
 * 发起一个amf请求，返回请求结果，如果请求状态不是200，则返回请求结果
 * @param string $url
 * @param string $method
 * @param mixed $args
 * @param bool $parse 是否需要解析http code是200的请求
 * @return mixed
 */
function exec_amf_request($url,$method,$args,$parse = true){
	if($GLOBALS['amfphp']['native'] === true && function_exists('amf_decode')){
		include_once(AMFPHP_BASE . "amf/io/AMFBaseSerializer.php");
		$serializer = new AMFBaseSerializer(); // Create a serailizer around the output stream
	}
	else{
		include_once(AMFPHP_BASE . "amf/io/AMFSerializer.php");
		$serializer = new AMFSerializer(); // Create a serailizer around the output stream
	}
	include_once(AMFPHP_BASE . "shared/util/MessageBody.php");
	$body = new MessageBody($method, '/1');
	$body->responseURI = $method;
	$body->setResults($args);
	$amfObj = new AMFObject();
	$amfObj->addBody($body);
	$data = $serializer->serialize($amfObj);
	require_once FRAMEWORK . '/net/HttpRequest.class.php';
	$req = new HttpRequest();
	$result = $req->post($url,$data,array('Content-Type' => 'application/x-amf'),5);
	if($result['http_code'] == '200' && $parse){
		return parse_amf_result($result);
	}
	return $result;
}
/**
 * 解析amf结果
 * @param array $result
 * @return mixed
 */
function parse_amf_result($result){
	if($GLOBALS['amfphp']['native'] === true && function_exists('amf_decode')){
		include_once(AMFPHP_BASE . "amf/io/AMFBaseDeserializer.php");
		$deserializer = new AMFBaseDeserializer($result['data']);
	}else{
		include_once(AMFPHP_BASE . "amf/io/AMFDeserializer.php");
		$deserializer = new AMFDeserializer($result['data']);
	}
	$amf_result_obj = new AMFObject($result['data']);
	$deserializer->deserialize($amf_result_obj);
	if($amf_result_obj->numBody() > 0){
		$amf_body = $amf_result_obj->getBodyAt();
		return $amf_body->getValue();
	}
	return $result;
}