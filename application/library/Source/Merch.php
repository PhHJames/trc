<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/23 0023
 * Time: 15:50
 * desc 商户的类库文件
 */
class Source_Merch{
    /**
     * 给商户发送异步的补单请求
     * $sorder_sn 	string  系统订单号
     * @return	array
     */
    public function sendRsyncSupplement($order_sn , $callback ){
        $log = Log_file::getInstance( array('filename' => "sendRsyncSupplement" ) );
        try{
            $res = $this->makeSupplementParams($order_sn);
            $params = $res['params'];
            $orderInfo = $res['orderInfo'];
            $merchInfo = $res['merchInfo'];
            $supplement_url = $merchInfo['supplement_url'];
            $ret = Network::RequestData($supplement_url,$params);
            $log->Write("info" , "给商户发送补单异步请求订单号：{$order_sn},发送参数：" . json_encode($params ,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . " , 商户响应：" . $ret );
            $callback($ret);
        }catch(Exception $e ){
            $log->Write("error"  ,  $e->getMessage());
            return false ;
        }
    }
    /**
     * 给商户发送异步的订单成功或者是失败请求
     * $sorder_sn 	string  系统订单号
     * @return	array
     */
    public function sendRsyncOrderReq($order_sn , $callback ){
        $log = Log_file::getInstance( array('filename' => "sendRsyncOrderReq" ) );
        try{
            $res = $this->makeNotifyOrdersParams($order_sn);
            $params = $res['params'];
            $orderInfo = $res['orderInfo'];
            $merchInfo = $res['merchInfo'];
            $notify_url = $orderInfo['notify_url'];
            $ret = Network::RequestData($notify_url,$params);
            $log->Write("info" , "给商户发送异步请求,订单号：{$order_sn},发送参数：" . json_encode($params ,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . " , 商户响应：" . $ret );
            $callback($ret);
        }catch(Exception $e ){
            $log->Write("error"  ,  $e->getMessage());
            return false ;
        }
    }
    /**
     * 生成补单的相关参数 准备发异步送给商户
     * $sorder_sn 	string  系统订单号
     * @return	array
     */
    public function makeSupplementParams( $order_sn ){
        $ordersModel = new OrdersModel();
        $orderInfo = $ordersModel->getInfo( array('order_sn' =>$order_sn ) );
        if( empty($orderInfo) ){
            throw new Exception("没有查询到订单信息");
        }
        $mid = $orderInfo['mid'];
        $MerchantModel = new MerchantModel();
        $merchInfo  = $MerchantModel->getInfo(array('mid' => $mid ) );
        if( empty($merchInfo) ){
            throw new Exception("没有查询到商户信息");
        }
        $supplement_url = $merchInfo['supplement_url'];
        if( empty($supplement_url) ){
            throw new Exception("没有查询到补单的异步地址");
        }
        $params = array(
            'order_sn' => $orderInfo['order_sn'],
            'merch_order_sn' => $orderInfo['merch_order_sn'],
            'trans_order_sn' => $orderInfo['trans_order_sn'],
            'money' =>  $orderInfo['money'],
            // 'real_money' => Common::foramtNumber($orderInfo['real_money']/100 , 2 ),
            'r_money' =>  $orderInfo['r_money'],
            'complete_date' => $orderInfo['complete_date'],
            'status' =>  intval($orderInfo['status']),
            'channel' =>  trim($orderInfo['channel']),
            'appid' => $merchInfo['appid'],
            //'sign' => $this->makeSign($merchInfo['appid'] , $merchInfo['appsecret'] ),
            'txid' => $orderInfo['txid'],
            'is_supplement' => 1 ,
        );
        $sign = $this->getSignature($params , $merchInfo['appsecret']);
        $params['sign'] = $sign ;
        return ['params' => $params , 'orderInfo' => $orderInfo , 'merchInfo' => $merchInfo  ]  ;
    }
    /**
     * 生成异步订单通知的相关参数 准备发异步送给商户
     * $sorder_sn 	string  系统订单号
     * @return	array
     */
    public function makeNotifyOrdersParams( $order_sn ){
        $ordersModel = new OrdersModel();
        $orderInfo = $ordersModel->getInfo( array('order_sn' =>$order_sn ) );
        if( empty($orderInfo) ){
            throw new Exception("没有查询到订单信息");
        }
        $mid = $orderInfo['mid'];
        $MerchantModel = new MerchantModel();
        $merchInfo  = $MerchantModel->getInfo(array('mid' => $mid ) );
        if( empty($merchInfo) ){
            throw new Exception("没有查询到商户信息");
        }
        $notify_url = $orderInfo['notify_url'];
        if( empty($notify_url) ){
            throw new Exception("没有查询到订单的异步地址");
        }
        $params = array(
            'order_sn' => $orderInfo['order_sn'],
            'merch_order_sn' => $orderInfo['merch_order_sn'],
            'trans_order_sn' => $orderInfo['trans_order_sn'],
            'money' =>  $orderInfo['money'] ,
            'r_money' =>  $orderInfo['r_money'],

            //'real_money' => Common::foramtNumber($orderInfo['real_money']/100 , 2 ),
            'complete_date' => $orderInfo['complete_date'],
            'status' =>  intval($orderInfo['status']),
            'channel' =>  trim($orderInfo['channel']),
            'appid' => $merchInfo['appid'],
           // 'sign' => $this->makeSign($merchInfo['appid'] , $merchInfo['appsecret'] ),
            'txid' => $orderInfo['txid'],
            'is_supplement' => 1 ,
        );
        $sign = $this->getSignature($params , $merchInfo['appsecret']);
        $params['sign'] = $sign ;
        return ['params' => $params , 'orderInfo' => $orderInfo , 'merchInfo' => $merchInfo  ]  ;
    }

    /**
     *  给商户发速提现结果通知
     * $order_no 	string  提现单号
     * @return	array
     */
    public function noticeMerchWithdrawResult($order_no = ''){
        $log = Log_file::getInstance( array('filename' => "send_merch_withdraw" ) );
        try{
            if( empty($order_no)){
                throw new Exception("缺失提现单号");
            }
            $MerchantWithDrawModel = new MerchantWithDrawModel();
            $info = $MerchantWithDrawModel->getInfo(['order_no' => $order_no]);
            if( empty($info)){
                throw new Exception("没有查询到提现记录");
            }
            $MerchantModel = new MerchantModel();
            $merchInfo  = $MerchantModel->getInfo(['mid' =>$info['mid'] ]);
            $notify_url = isset($info['notify_url']) ? $info['notify_url'] :"";
            if( empty($notify_url) ){
                throw new Exception("没有异步通知地址无法进行通知。。");
            }
            $params = array(
                'order_no' => $order_no,
              //  'sign' => $this->makeSign($merchInfo['appid'] , $merchInfo['appsecret'] ),
                'txid' => $info['txid'] ? $info['txid']  : '' ,
                'status' => $info['status'] ,
                'fixed_poundage' => $info['fixed_poundage'] ? $info['fixed_poundage']  : '' ,
                'money' => $info['money'] ? $info['money']  :  0  ,
                'merch_order' => $info['merch_order'],
            );
            $sign = $this->getSignature($params , $merchInfo['appsecret']);
            $params['sign'] = $sign ;

            $ret = Network::RequestData($notify_url,$params);
            $log->Write("info" , "给商户发送提现通知结果 系统单号：{$order_no},发送参数：" . json_encode($params ,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . " , 商户响应：" . $ret );
            if( strtolower($ret ) != 'success' ){
                throw new Exception("异步通知提现记录商户返回结果:{$ret} , 提现单号：{$order_no}");
            }
            return true;
        }catch( Exception $e ){
            Log_Db::WriteLog("noticeMerchWithdrawResult" ,"提现结果通知商户：" .  $e->getMessage());
            return false ;
        }

    }


    public function makeSign( $appid , $appsecret ){
        return strtolower(md5($appid .$appsecret)) ;
    }
    //生成新的签名
    /**
     * 签名生成算法
     * @param  array  $params API调用的请求参数集合的关联数组，不包含sign参数
     * @param  string $appsecret 签名的密钥即
     * @return string 返回参数签名值
     */

    public function getSignature($params = [] , $appsecret ){
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
        $str .= $appsecret;
        return strtolower(md5($str));

    }
    //检测签名,如果成功返回商户数据
    public function checkSign($params){
        $appid = isset($params['appid']) ? trim($params['appid']) :"";
        $sign = isset($params['sign']) ? trim($params['sign']) :"";
        if( empty($appid )){
            throw new Exception(ErrorCode::$noAppid[API_MSG_NAME] ,ErrorCode::$noAppid[API_CODE_NAME]   );
        }
        if( empty($sign )){
            throw new Exception(ErrorCode::$noSign[API_MSG_NAME],ErrorCode::$noSign[API_CODE_NAME]   );
        }
        $MerchantModel = new MerchantModel();
        $info = $MerchantModel->getInfo(array('appid' => $appid));
        if( empty($info) ){
            throw new Exception(ErrorCode::$merchError[API_MSG_NAME]  ,ErrorCode::$merchError[API_CODE_NAME]  );
        }
        //签名检测
        /*
        if( $sign  != strtolower(md5($appid .$info['appsecret'] )) ){
            throw new Exception(ErrorCode::$signError[API_MSG_NAME]  ,ErrorCode::$signError[API_CODE_NAME]   );
        }*/
        //print_r($params);

        $new_sign = $this->getSignature($params,$info['appsecret']);
        if( $sign  !=  $new_sign ){
            throw new Exception(ErrorCode::$signError[API_MSG_NAME]  ,ErrorCode::$signError[API_CODE_NAME]   );
        }
        return $info;
    }
}