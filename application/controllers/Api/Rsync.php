<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/23 0023
 * Time: 23:54
 */
class Api_RsyncController extends Api_CommonController
{
    public function init()
    {
        parent::init();
    }

    public function rsync_trc20Action(){
        $str = file_get_contents("php://input") ;
        $result=json_decode($str,true);
        if( empty($result )){
            echo "error";
            exit;
        }
        $businessOrders = Common::ImportBusiness("Orders" , "Api");
        echo  "数据已接收到了\n";
        try{
            $businessOrders->do_success_trc20_order($result);
        }catch(Exception $e ){
            echo  "trc20异步回调出错了，错误信息： " . $e->getMessage() . "\n" ;
        }
    }

    //接收trx的交易事件
    public function trx_transAction(){
        $str = file_get_contents("php://input") ;
        $result=json_decode($str,true);
        if( empty($result )){
            echo "error";
            exit;
        }
        $logFile = Log_file::getInstance();
        //echo "接收到数据：" . $str . "\n";
        echo "OK";
        $logFile->Write("info" , $str);
        $BlockTrans = Common::ImportBusiness("BlockTrans" , "Api");
        try{
            $BlockTrans->do_trx_trans($result);
        }catch(Exception $e ){
            echo  "TRX消息处理失败，错误信息： " . $e->getMessage() . "\n" ;
        }
    }
    //接收usdt的交易事件
    public function usdt_transAction(){
        $str = file_get_contents("php://input") ;
        $result=json_decode($str,true);
        if( empty($result )){
            echo "error";
            exit;
        }
        $logFile = Log_file::getInstance();
        //echo "接收到数据：" . $str . "\n";
        echo "OK \n";
        $logFile->Write("info" , $str);
        $BlockTrans = Common::ImportBusiness("BlockTrans" , "Api");
        try{
            $BlockTrans->do_usdt_trans($result);
        }catch(Exception $e ){
            echo  "USDT消息处理失败，错误信息： " . $e->getMessage() . "\n" ;
        }
    }
    //ztpay的异步回调通知
    /*public function ztpayCallbackAction(){
        $data=file_get_contents("php://input");
        Log_Db::WriteLog("ztpayCallback" , $data );
        $result=json_decode($data,true);
        if( empty($result )){
            echo "error";
            exit;
        }
        $data = isset($result['data']) ? $result['data'] : [];
        $sign =  isset($result['sign']) ? $result['sign'] :"";
        $address =  isset($data['to']) ? $data['to'] :"";
        $amount =  isset($data['amount']) ? $data['amount'] :"";
        $hash =  isset($data['hash']) ? $data['hash'] :"";
        $type =  isset($data['type']) ? $data['type'] :"";
        $state =  isset($data['state']) ? $data['state'] :"";
        $Source_Ztpay = new Source_Ztpay();
        $new_sign =  $Source_Ztpay->getSign($data , $Source_Ztpay->appsecret );
        if($sign != $new_sign ){
            echo "签名错误";
            exit;
        }
        $businessOrders = Common::ImportBusiness("Orders" , "API");
        if( $state == 1 AND $type == 1 ){ //收到币的成功通知
            try{
                $businessOrders->do_success_order( $address ,$amount ,$hash );
                echo "success";
            }catch(Exception $e ){
                echo "error:" . $e->getMessage() . "\n";
            }
        }else{
            echo "未知类型 暂不处理。。。。";
        }
    }*/
}