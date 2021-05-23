<?php
/***公共资源类****/
class Common {
	/**
	* js返回json数据,格式要统一
	* $code	int  返回的错误码
	* $msg	string 返回的错误信息
	* $data array 返回的数组
	*/
	public static function EchoResult($code = 0 , $msg = '' , $data = array() )
	{
		$result = array(
			'code' => $code ,
			'msg' => $msg ,
			'data' => $data
		);
		echo json_encode($result ,  JSON_UNESCAPED_UNICODE);
		exit;
	}
	public static function cmdJson($cmd , $data = array() ){
        $result = array('cmd' => $cmd , 'data' =>$data );
        return json_encode($result,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES  );
    }
	/**
	 * 导入文件并且进行实例化 ,默认导入的是business这个文件夹下面的文件
	 * $filename	   string $layer 模型层名称
	 * $folder 文件夹
	 * return object
	 */
	public static function ImportBusiness($name = null  , $folder = ''  ){
		$layer = "business";
		$class=$name.ucfirst($layer);
		static $_obj=array();//定义一个静态的变量 避免重复的new对象
		$obj_key = '';
		if($folder != '' ){
			$obj_key = md5($folder . $class);
		}else{
			$obj_key = md5($class);
		}
		if(isset($_obj[$obj_key])){
			return $_obj[$obj_key];
		}
		$filename = '' ;
		if($folder != '' ){
			$filename = APP_PATH."/application/{$layer}/{$folder}/{$class}.php";
		}else{
			$filename = APP_PATH."/application/{$layer}/{$class}.php";
		}
		if(!file_exists($filename)){
			exit("file {$class}.php is not exists filename: {$filename}");
		}
		Yaf_loader::import($filename);//导入类库（业务逻辑类库之类的）
		$_obj[$obj_key] = new $class();
		return $_obj[$obj_key];
	}
    /**
     * 返回url
     * $url	   格式 控制器/操作
     * @extra 其他参数 数组形式 例如 array('hist' => 1 , 'number'=>44)
     */
    public static function U($url = '', $extra = array()) {
        global $_G;
        $lastUrl = '';
        $tempUrl = '';
        //不在urlMap里面
        $info = parse_url($url);
        list($module, $action) = explode("/", $info['path']);
        $extra = (is_array($extra) && !empty($extra)) ? ('?' . http_build_query($extra)) : '';
        return $_G['config']['domain']['domain']['ADMIN_DOMAIN']  .$module ."/" . $action . $extra;
    }
	/**
	* 处理form 提交的参数过滤
	* $string	string  需要处理的字符串或者数组
	* @return	string 返回处理之后的字符串或者数组
	*/
	public static function Dtrim($string){
		if(is_array($string)) {
			$keys = array_keys($string);
			foreach($keys as $key) {
				$val = $string[$key];
				unset($string[$key]);
				$string[trim($key)] = self::Dtrim($val);
			}
		} else {
			$string = trim($string);
		}
		return $string;
	}
	/**
	* 处理form 提交的参数过滤
	* $string	string  需要处理的字符串或者数组
	* $force	boolean  强制进行处理
	* @return	string 返回处理之后的字符串或者数组
	*/
	public static function Daddslashes($string, $force = 1) {
		if(is_array($string)) {
			$keys = array_keys($string);
			foreach($keys as $key) {
				$val = $string[$key];
				unset($string[$key]);
				$string[addslashes($key)] = self::daddslashes($val, $force);
			}
		} else {
			$string = addslashes($string);
		}
		return $string;
	}
	//请参考php内置的方法 htmlspecialchars
	public static function Dhtmlspecialchars($string, $flags = ENT_COMPAT) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = self::dhtmlspecialchars($val, $flags);
			}
		} else {
			$charset = 'UTF-8';
			$string = htmlspecialchars($string, $flags, $charset);
		}
		return $string;
	}
	//请参考 php 内置方法 stripslashes
	public static function Dstripslashes($string) {
		if(empty($string)) return $string;
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = self::dstripslashes($val);
			}
		} else {
			$string = stripslashes($string);
		}
		return $string;
	}


	/*
	*	函数名称：VerifyId()
	*	函数作用：校验提交的ID类值是否合法
	*	参　　数：$id: 提交的ID值
	*	返 回 值：返回处理后的ID
	*
	*/
	public static function VerifyId($id=null) {
		if (!$id) {
			return 0;
		} // 是否为空判断
		elseif (self::inject_check($id)) {
			return 0;
		} // 注射判断
		elseif (!is_numeric($id)) {
			return 0 ;
		} // 数字判断
		$id = intval($id); // 整型化
		return $id;
	}
	/*
	 *检测提交的值是不是含有SQL注射的字符，防止注射，保护服务器安全
	*参　　数：$sql_str: 提交的变量
	*返 回 值：返回检测结果，ture or false
	*/
	public static function InjectCheck($sql_str) {
		return @eregi('select|insert|and|or|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str); // 进行过滤
	}
    /*
     *	保留小数
     *	参　　数：$string: 格式化的字符串
     *  参　　数：$number: int 保留的小数个数
     *	返 回 值：返回处理后的ID
        *
     */
	public static function foramtNumber( $string ,int  $number = 8  , $is_float = 0  ){
	    if($is_float){
            return floatval( sprintf("%.{$number}f",$string) );
        }
        return ( sprintf("%.{$number}f",$string) );
    }

}
