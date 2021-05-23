<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/23 0023
 * Time: 23:54
 */
class Api_IndexController extends Api_CommonController
{
    public function init()
    {
        parent::init();
    }
    public function indexAction(){
        echo " this is API  控制器的API目录就是API存放的地方 ， 业务放在 Business/Api 目录";
    }
    
      public function testAction()
    {
         $key = "ejSPciW0n5i4sTGB6vglZ9BIIb63ojRL";
        $params = [
            "appid"=>"iNeTJhTIeRuRvUjX",
            "address"=>"TYzSjiUvx48Lcpe8dBJSdg8e1MiD1WiTD8",
            "amount"=>"100.00",
            "notify_url"=>"https://baidu.com",
            "merch_order"=>"TEST".time(),
        ];


        $str = '';  //待签名字符串
        //先将参数以其参数名的字典序升序进行排序
        ksort($params);
        //遍历排序后的参数数组中的每一个key/value对
        foreach ($params as $k => $v) {
            //为key/value对生成一个key=value格式的字符串，并拼接到待签名字符串后面
            if( $k == 'sign' ){
                continue ;
            }
            if( empty($v) ){//排除非空的值 如果值是0 那么也不参与计算
                continue ;
            }
            $str .= $k . $v;
        }
        $str .= $key;
        $sign =strtolower(md5($str));
        $params["sign"] = $sign;


        $oCurl = curl_init ();

            $aPOST = array ();
            foreach ( $params as $key => $val ) {
                $aPOST [] = $key . "=" . urlencode ( $val );
            }
            $strPOST = join ( "&", $aPOST );

        curl_setopt ( $oCurl, CURLOPT_URL, "http://trc.yunshipay.com/Api_Merchwithdraw/apply" );
        curl_setopt ( $oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $oCurl, CURLOPT_POST, true );
        curl_setopt ( $oCurl, CURLOPT_POSTFIELDS, $strPOST );
        $sContent = curl_exec ( $oCurl );
        curl_close ( $oCurl );

        var_dump($sContent);
    }
}