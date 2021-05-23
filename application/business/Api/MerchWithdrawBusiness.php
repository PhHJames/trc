<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-04-26 22:59:32
 * @des 商户 业务文件
 */
class MerchWithdrawBusiness extends AbstractModel {
    //申请提现
    public function apply($params = [] ){
        $MerchApiCommonBusiness = Common::ImportBusiness("MerchApiCommon" , "Api");
        //检测签名
        $merch = $MerchApiCommonBusiness->checkSign($params);
        //白名单
        $MerchApiCommonBusiness->checkWhiteIp($merch);
        $address = isset($params['address'])  ? $params['address'] : '' ;
        $money = isset($params['amount'])  ? $params['amount'] :  0 ;
        $Source_Exchange = new Source_Exchange();
        $channel = new ChannelModel();
        $rate =  $Source_Exchange->CnyUSDTHuansuan(2 ,$channel->getChannel()[0]["channel"]);

        $money = $money/$rate;

        $notify_url =  isset($params['notify_url'])  ? urldecode($params['notify_url'])  :   '' ;
        $merch_order = isset($params['merch_order'])  ? $params['merch_order'] : '' ;//商户订单号
        
        if( empty($merch_order)){
            throw  new Exception("缺失提现单号");
        }
        $stringClass = new StringClass();
        if($stringClass->utf8_str($merch_order) != 1 ){
            throw new Exception( "提现单号格式错误" );
        }
        $len = $stringClass->abslength($merch_order);
        if( $len < 6 || $len > 64 ){
            throw new Exception( "提现单号长度必须在6-64位" );
        }
        
        if( empty($address)){
            throw  new Exception("请输入目标币地址");
        }
        if( empty($money)){
            throw  new Exception("请输入提币数量");
        }
        
        $fen =  ($money * pow(10 , NUMBER_FORMAT) ) ;
        if( $fen  <= 0 ){
            throw new Exception( "币数量格式不对" );
        }
        $mid = $merch['mid'];
        //提现频率检测
        $redis_key = "withdraw_{$mid}";
        $RedisClass = RedisClass::getInstance(3);

        if($RedisClass->GET($redis_key) == 1 ){
            throw new Exception( "请求频率过高 请稍后重新尝试！！" );
        }
        $MerchantsBankModel = new MerchantsBankModel();
        $MerchantWithDrawModel = new MerchantWithDrawModel();
        $bankInfo = $MerchantsBankModel->getInfo( array('address' =>$address , 'mid' => $mid , 'status' => 2   ) );
        if( empty($bankInfo )){
            $bankid = $MerchantsBankModel->insertData(["mid"=>$mid,"address"=>$address,"status"=>2,"create_time"=>date("Y-m-d H:i:s",time()),"remark"=>"API生成","channel"=>$channel->getChannel(),"name"=>"商户提币地址"]);

            $bankInfo = $MerchantsBankModel->getInfo(array("id"=>$bankid));
           // throw new Exception( "没有查到相关的收款币地址信息" );
        }
        $merchWithBusiness = Common::ImportBusiness("Withdraw" , "Merch");
        $order_no = $merchWithBusiness->doWithdraw(
            ['bank_id' => $bankInfo['id'] , 'money' => $money  , 'notify_url' => $notify_url] , $mid , 1 , $merch_order 
        );
        $withdraw = $MerchantWithDrawModel->getInfo(['order_no' => $order_no]);
        $RedisClass->SET($redis_key , 1 , 10 );
        return $withdraw;
    }
    public function query($params = array() ){
        $MerchApiCommonBusiness = Common::ImportBusiness("MerchApiCommon" , "Api");
        //检测签名
        $merch = $MerchApiCommonBusiness->checkSign($params);
        //白名单
        $MerchApiCommonBusiness->checkWhiteIp($merch);
        $order_no = isset($params['order_no']) ? trim($params['order_no']) :"";
        $merch_order = isset($params['merch_order']) ? trim($params['merch_order']) :"";

        if( empty($order_no ) AND empty($merch_order) ){
            throw new Exception("系统单号或者是商户单号必须传递"   );
        }
        $MerchantWithDrawModel = new MerchantWithDrawModel();
        if($order_no){
            $order = $MerchantWithDrawModel->getInfo(array('mid' => $merch['mid'] , 'order_no' => $order_no ));
        }else{
            if($merch_order){
                $order = $MerchantWithDrawModel->getInfo(array('mid' => $merch['mid'] , 'merch_order' => $merch_order ));
            }
        }
        if( empty($order )){
            throw new Exception("没有查询到提现记录"   );
        }
        $res = array(
            'order_no' => $order['order_no'] ,
            'status' =>  intval($order['status']),
            'merch_order' => $order['merch_order'],
        );
        return $res ;
    }
}