<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2016-06-20 20:57:56
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2016-08-16 21:55:33
 * @des 请求接口
 */
class RequestApi{
	public static $appid = "BED0CD835210C562" ;
	public static $secret = "50aa98e5bed0cd835210c562eb0a0159" ;
	public static function getRequestData($requestUrl = '' , $request_data , $time = 3  , $curl_info = null ){
		$currentTime = time();
		$sign = md5( self::$secret . $currentTime );
		$header = array(
			'appid:'.self::$appid ,
			'time:'.$currentTime , 
			'sign:' . $sign ,
		) ;
        //print_r($header);
		$curlOptions = array(
            CURLOPT_URL => $requestUrl, //访问URL
            CURLOPT_RETURNTRANSFER => true, //获取结果作为字符串返回
            CURLOPT_FOLLOWLOCATION => FALSE,
            CURLOPT_HEADER => false, //获取返回头信息
            CURLOPT_POST => true, //发送时带有POST参数
            CURLOPT_POSTFIELDS => $request_data, //请求的POST参数字符串
            CURLOPT_CONNECTTIMEOUT => $time ,//超时时间设置
            CURLOPT_TIMEOUT => $time ,
            CURLOPT_SSL_VERIFYPEER => false ,
            CURLOPT_SSL_VERIFYHOST => false ,
            CURLOPT_CUSTOMREQUEST => "POST" , 
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0 , 
			CURLOPT_HTTPHEADER => $header,
        );
		$data =  Network::HttpRequest($curlOptions ,  $curl_info ,3 , 1 );
        //print_r($data);
       /* echo "<pre>";
        print_r($data);*/
        /*$data =  Network::HttpRequest($curlOptions ,  $curl_info  ,3 , 1 );
        
        */
		return json_decode($data , true ) ;
	}
	
}