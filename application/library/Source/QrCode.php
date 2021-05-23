<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2016-06-27 16:54:41
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2016-07-07 09:48:54
 */
use baiduapi\AipOcr;
const APP_ID = '17061689';
const API_KEY = 'q1WIIlikAOuGanf4OVZkPiqT';
const SECRET_KEY = 'xvdVGGSXB9aTdy65ZI6uNkvqDFFt13yM';
class Source_QrCode{
    public $log_type = "decode_qr";
    public function __construct(){


    }

    //解析二维码
    //file 是绝对的路径
    //$x_path 是相对路径
    public function decodeQr($file , $x_path = '' ){
        global $_G;
        $text =  $this->caoliaoDecode($file);
        if( empty($text )){
            $picUrl = $_G['config']['domain']['domain']['UPLOADS_HOST'] . $x_path;
            $text = $this->readQrCode($picUrl);
        }
        return $text;
    }
    //草料解析二维码
    public function caoliaoDecode($file){
        $resp = $this->uploadImageRemote($file);
        Log_Db::WriteLog($this->log_type , "草料上传图片到草料返回：" . $resp  );
        $resp = json_decode($resp , true );
        $path = isset($resp['data']['path']) ? $resp['data']['path'] :"";
        if( empty($path )){
            return ;
        }
        $requestUrl = "https://cli.im/Api/Browser/deqr";
        $header = [
            'Referer: https://cli.im/deqr',
            'Cookie: '.'_ga=GA1.2.1787026061.1556205758; Hm_lvt_cb508e5fef81367bfa47f4ec313bf68c=1594698628,1594698646,1594699943,1595248831; Hm_lvt_56f2b333790f96ef48c2bd70c8c13f16=1592451581; claf=c1_1.a2_2.s1_1.h1_1.i1_2.i2_2.j1_1.k1_1; qrs=353055862.353056325.359791159; Hm_cv_cb508e5fef81367bfa47f4ec313bf68c=1*price*1; cvid=243351007; _gat=1; Hm_lpvt_cb508e5fef81367bfa47f4ec313bf68c=1595249325; SERVERID=04b0df77440bfdb7b7648e4c9c6ffbcf|1595249082|1595249082',
            'Origin: https://cli.im'
        ];
        $request_data =['data' => $path ];
        //print_r($header);
        $curlOptions = array(
            CURLOPT_URL => $requestUrl, //访问URL
            CURLOPT_RETURNTRANSFER => true, //获取结果作为字符串返回
            CURLOPT_FOLLOWLOCATION => FALSE,
            CURLOPT_HEADER => false, //获取返回头信息
            CURLOPT_POST => true, //发送时带有POST参数
            CURLOPT_POSTFIELDS => http_build_query($request_data), //请求的POST参数字符串
            CURLOPT_CONNECTTIMEOUT => 5 ,//超时时间设置
            CURLOPT_TIMEOUT => 5 ,
            CURLOPT_SSL_VERIFYPEER => false ,
            CURLOPT_SSL_VERIFYHOST => false ,
            CURLOPT_CUSTOMREQUEST => "POST" ,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0 ,
            CURLOPT_HTTPHEADER => $header,
        );
        $resp =  Network::HttpRequest($curlOptions ,  null  );
        Log_Db::WriteLog($this->log_type , "调用草料的二维码解析返回：" . $resp  );
        $respData = json_decode($resp , true );
        $text =  isset($respData['data']['RawData']) ? $respData['data']['RawData'] :"";
        if( empty($text )){
            $text = $this->readQrCode( $path );
        }
        return $text ;
    }
    //把图片上传到草料
    public function uploadImageRemote($path ){
        $curl = curl_init();
        if (class_exists('\CURLFile')) {
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
            $data = array('Filedata' => new \CURLFile(realpath($path)));//>=5.5
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }
            $data = array('Filedata' => '@' . realpath($path));//<=5.5
        }
        $header = [
            'Referer: https://cli.im/deqr',
            'Cookie: '.'_ga=GA1.2.1787026061.1556205758; Hm_lvt_cb508e5fef81367bfa47f4ec313bf68c=1594698628,1594698646,1594699943,1595248831; Hm_lvt_56f2b333790f96ef48c2bd70c8c13f16=1592451581; claf=c1_1.a2_2.s1_1.h1_1.i1_2.i2_2.j1_1.k1_1; qrs=353055862.353056325.359791159; Hm_cv_cb508e5fef81367bfa47f4ec313bf68c=1*price*1; cvid=243351007; _gat=1; Hm_lpvt_cb508e5fef81367bfa47f4ec313bf68c=1595249325; SERVERID=04b0df77440bfdb7b7648e4c9c6ffbcf|1595249082|1595249082',
            'Content-Type: multipart/form-data',
            'Origin: https://cli.im'
        ];
        curl_setopt($curl, CURLOPT_URL, "https://upload.api.cli.im/upload.php?kid=cliim");
        curl_setopt($curl, CURLOPT_POST, 1 );
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 跳过证书验证（https）的网站无法跳过，会报错
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书验证
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:78.0) Gecko/20100101 Firefox/78.0");

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($curl);
        $error = curl_error($curl);
        return $result ;
    }

    //解析二维码图片(根据远程的图片地址)
    //参考：http://goqr.me/api/doc/read-qr-code/
    public function readQrCode( $path ){
        $url = urlencode( urldecode($path));
        $reqUrl = "https://api.qrserver.com/v1/read-qr-code/?fileurl={$url}";
        $curl = curl_init();
        $header = [

        ];
        curl_setopt($curl, CURLOPT_URL, $reqUrl);
        curl_setopt($curl, CURLOPT_POST,  0  );
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 跳过证书验证（https）的网站无法跳过，会报错
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书验证
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:78.0) Gecko/20100101 Firefox/78.0");

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $resp = curl_exec($curl);
        $error = curl_error($curl);
        Log_Db::WriteLog($this->log_type , "调用goqr.me的二维码解析返回：{$resp}, 图片地址：{$path}"  );
        $respData = json_decode($resp , true );
        $text =  isset($respData[0]['symbol'][0]['data']) ? $respData[0]['symbol'][0]['data']:"";
        return $text;
    }

    /**
     * 解析二维码的图片内容
     * 闲鱼的二维码图片验证
     */
    public function qr_code_valid_xianyu($path){
        require_once APP_PATH . "/application/library/baiduapi/AipOcr.php";
        $client =  new AipOcr(APP_ID, API_KEY, SECRET_KEY);
        $image = '';
        $image =  @file_get_contents($path);
        if(empty($image))
        {
            throw new \Exception("图片为空");
        }
        $results = $client->basicAccurate($image);
        if(!empty($results['error_code'])){
            throw new \Exception($results['error_msg']);
        }
        if(empty($results['words_result_num']))
        {
            throw new \Exception("未能识别该图");
        }
        $words_result = isset($results['words_result']) ?$results['words_result']:[];
        if(empty($words_result))
        {
            throw new \Exception("未能识别该图,该图没有识别结果");
        }
        $money = 0 ;
        $goods_name = '' ;
        Log_Db::WriteLog("qr_code_valid_xy",$results);
        foreach ($words_result as $kk => $vv ){
            $words = isset($vv['words']) ? $vv['words'] :"";
            if( stripos($words , "￥" ) !== false ){
                $money = str_replace("￥" , "" , $words ) ;
            }
            if( stripos($words , "担保交易" ) !== false ){
                $goods_name = str_replace("担保交易" , "" , $words ) ;
                $goods_name = str_replace("-" , "" , $goods_name ) ;
            }
        }
        $return = ['money' => $money , 'goods_name' => $goods_name  ] ;
        return $return ;
    }

}