<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/23 0023
 * Time: 20:58
 */
class Shell_TestController extends Shell_CommonController
{
    public function init()
    {
        parent::init();
    }
    //最新事件
    public function latestEventAction(){
        $Tron_Address_Address = new Tron_Address_Address();
        /*echo $Tron_Address_Address->hexString2Utf8("0x8a4a39b0e62a091608e9631ffd19427d2d338dbd");
        exit;*/
        while( true ){
            $url = "https://api.trongrid.io/v1/blocks/latest/events?only_confirmed=true&limit=100";
            $resp = Network::RequestData($url);
            $result = json_decode($resp , true );
            $data = isset($result['data']) ? $result['data'] : [];
            if( empty($data) ){
                continue;
            }
            foreach ($data as $kk => $vv ){
                $contract_address = $vv['contract_address'];
                if($contract_address != 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t' ){
                    //continue ;
                }
                $event_name = isset($vv['event_name'])?$vv['event_name']:"";
                if($event_name != 'Transfer' ){
                    //continue ;
                }
                $transaction_id = isset($vv['transaction_id'])?$vv['transaction_id']:"";
                $from = isset($vv['result']['from'])?$vv['result']['from']:"";
                $to = isset($vv['result']['to'])?$vv['result']['to']:"";
                //$from = $Tron_Address_Address->hexString2Address($from);
                //$to = $Tron_Address_Address->hexString2Address($to);
                $str =   "[" . date("Y-m-d H:i:s" ,time()) . "] from:{$from} , to:{$to} transaction_id:{$transaction_id} \n ";
                file_put_contents("./tt.txt" , $str , FILE_APPEND );
                if( stripos($from , "0x") !== false ){
                    continue ;
                }
                echo $str;
            }
            //print_r($data);
            sleep(3);
        }
    }

}