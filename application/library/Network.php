<?php
/**
 * File: Network.php
 * Author: 不要复制我的代码
 * Date: 2012-03-01
 */
class Network{
	//获取客户端的ip地址
	public static  function GetClientIp(){
        $ip = '' ;
        if(!empty($_SERVER["REMOTE_ADDR"])){
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        return $ip ;

		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		elseif(!empty($_SERVER["REMOTE_ADDR"])){
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		else{
			$ip = "Unknown";
		}
		return $ip;
	}
	/*发送HTTP请求*/
	public static function HttpRequest($curlOptions,$curl_info=null)
	{
		/* 设置CURLOPT_RETURNTRANSFER为true */
		if (!isset($curlOptions[CURLOPT_RETURNTRANSFER]) || $curlOptions[CURLOPT_RETURNTRANSFER] == false) {
			$curlOptions[CURLOPT_RETURNTRANSFER] = true;
		}
		/* 初始化curl模块 */
		$curl = curl_init();
		/* 设置curl选项 */
		curl_setopt_array($curl, $curlOptions);
		/* 发送请求并获取响应信息 */
		$responseText = '';
		try {
			$responseText = curl_exec($curl);
			if (($errno = curl_errno($curl)) != CURLM_OK) {
				$errmsg = curl_error($curl);
				//throw new \Exception($errmsg, $errno);
				$responseText = false;
			}
		} catch (Exception $e) {
			//exceptionDisposeFunction($e);
			//print_r($e);
			$responseText = false;
		}
		if ($curl_info != null) {
			$responseText = array(
					'responseText' => $responseText,
					'curl_info' => curl_getinfo($curl),
			);
		}
		/* 关闭curl模块 */
		curl_close($curl);
		/* 返回结果 */
		return $responseText;
	}

	public static function RequestData($url,$data = null){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS,  http_build_query($data));
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}	
	public static function RawRequestData($url , $data = array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POST,           1 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,      json_encode($data , JSON_UNESCAPED_UNICODE) ); 
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 
        $result=curl_exec($ch);
        curl_close($ch);
        $ret = json_decode($result, true);
        return $ret;
    }
}