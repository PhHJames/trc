<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2021/1/14 0014
 * Time: 23:00
 */
class Source_Exchange{
    /**
     * 人民币和USDT换算 https://market.aliyun.com/products/57000002/cmapi011221.html#sku=yuncode522100006
     * 1:人民币换算USTD 2:ustd 换算人民币
     * @return
     */
    public function CnyUSDTHuansuan($type = 1 ,$channel = ''){
        $channelModel = new ChannelModel();
        $channelInfo = $channelModel->getInfo(array('channel' => $channel));
        if( empty($channelInfo['channel'] )){
            return ;
        }

        $extra = $channelInfo['extra'];
        $extraData = @json_decode($extra , true );
        $u_to_rmb  = isset($extraData['u_to_rmb']['value']) ? $extraData['u_to_rmb']['value'] : 0 ;
        if( empty($u_to_rmb)){
            return ;
        }
        if($type == 2 ){
            return $u_to_rmb;
        }
        $rmb_to_u = Common::foramtNumber((1 / $u_to_rmb) , 5  );
        return $rmb_to_u;
    }



    /*public function CnyUSDTHuansuan($type = 1 ,$channel = ''){
        $ALI_APPCODE = Source_Sysconfig::getInstance()->getVal("ALI_APPCODE");
        $curl = curl_init();
        $url = "https://jisuhuilv.market.alicloudapi.com/exchange/convert?amount=1&from=CNY&to=USD";
        if($type == 2 ){
            $url = "https://jisuhuilv.market.alicloudapi.com/exchange/convert?amount=1&from=USD&to=CNY";
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: APPCODE $ALI_APPCODE",
            ),
            CURLOPT_SSL_VERIFYPEER => false ,
            CURLOPT_SSL_VERIFYHOST => false ,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            Log_Db::WriteLog("CnyToUSDT" , "人民币转换USDT错误：" . $err );
            return ;
        } else {
            $result = json_decode($response , true ) ;
            $rate = isset($result['result']['rate']) ? $result['result']['rate'] :"";
            if( empty($rate )){
                Log_Db::WriteLog("CnyToUSDT" , "人民币转换USDT错误：返回信息：" . $response   );
                return ;
            }
            return $rate;
        }
    }*/

}