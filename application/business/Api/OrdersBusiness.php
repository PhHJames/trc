<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-04-26 22:59:32
 * @des 商户 业务文件
 */
use Pheanstalk\Pheanstalk;
class OrdersBusiness extends AbstractModel {
    //下单
    public function placeOrder($params = array() ){

        global $_G;
        //检测签名
        $merch = $this->checkSign($params);
        //检测频率
        $this->checkVisit($merch['mid'] , 'place_order'  , $merch['num_place_order']);

        //检测商户
        $this->checkMerch($merch);
        //白名单
        $this->checkWhiteIp($merch);
        $agent_id = intval($merch['agent_id']) ;

        $channel = isset($params['channel']) ? $params['channel'] : "";

        $place_m_type = isset($params['place_m_type']) ? $params['place_m_type']  : 1 ;


        //汇率计算
        $rate_data = $this->getMoneyRateData( $channel , $place_m_type);
        $rate = $rate_data['rate'];
        $f_rate = $rate_data['f_rate'];
        
        $r_money = isset($params['money']) ? trim($params['money']) :"";//人民币 默认是人民币
        $money = Tools::number_format($r_money * $rate , NUMBER_FORMAT  )  ;//换算出的金额这个是usdt数

        //这个地方换算下
        if( $place_m_type != 1  ){
            $money = $params['money'] ;
            $r_money = $params['money'] = $f_rate * $params['money']; //换算为人民币
        }
        //echo $place_m_type;die();
        //echo $r_money;die();

        $sparams = $params ; 
        $sparams['money'] = $r_money ; //人民币
        //检测参数
        $data = $this->checkOrder($sparams , $merch);

        $channelInfo = $data['channelInfo'];//通道信息
        $feeData = $data['feeData'];//商户的费率信息
        $fee = $feeData['fee']; //商户费率 小数点的
        $channelExtra = json_decode( $channelInfo['extra'] , true  );
        $agentChannel = $this->checkAgentChannel($agent_id , $channelInfo);


        $Source_Exchange = new Source_Exchange();






        $order_sn = Tools::build_order_no(32);
        $merch_order_sn = isset($params['merch_order_sn']) ? trim($params['merch_order_sn']) :"";



        $client_ip = isset($params['client_ip']) ? trim($params['client_ip']) :"";
        $real_money = Tools::number_format($money *  (1 - $feeData['fee'] ) , NUMBER_FORMAT )  ;//实际到账多少

        $p_real_money = 0  ; //平台赚的钱

        $agent_channel_fee = isset($agentChannel['fee']) ? $agentChannel['fee'] : 0 ;//代理商开的费率
        $agent_real_money = 0 ;//实际到代理商账户里面的钱【也就是代理赚的钱】
        $profit = $money   - $real_money  ;//总盈利 包含了 成本 也包含了代理拿的钱
        $p_real_money = 0 ; //平台实际赚的钱

        if( $agent_id > 0 ){
            if($agent_channel_fee * 1000000 == 0 ){//如果代理开的费率是0
                $agent_real_money =  0 ; //代理赚的钱
                $p_real_money = $profit  ;
            }else{
                $agent_real_money = ($money  * ( $fee - $agent_channel_fee) ) ; //代理赚的钱  下单金额 * 赚的费率
                $p_real_money =  ($profit - $agent_real_money )  ;
            }
        }else{
            //没有代理的情况的时候
            $p_real_money = ($profit ) ;
        }
        

        $qrCode = $this->matchQrCode($money , $merch , $feeData);
        if( empty($qrCode )){
            throw new \Exception( "暂无匹配的二维码,请重新更换支付金额重新尝试" , 0  );
        }
        
        ###################这个地方为了防止并发在做一次判断######################
        $s_m = $this->checkMatchAddressMoneyAllow($money , $qrCode['content'] );
        if( !$s_m ){
            throw new \Exception( "系统检测到支付金额目前暂未找到相对应的收款地址，请更换付款金额" , 0  );
        }
        ##########################################################################


        $orderInsert = array(
            'r_money' => $r_money ,
            'e_rate' => $rate ,
            'f_rate' =>$f_rate,
            'order_sn' => $order_sn ,
            'merch_order_sn' =>$merch_order_sn ,
            'money' =>$money ,
            'real_money'=>$real_money,
            'm_fee'=>$feeData['fee'],
            'mid'=>$merch['mid'],
            'create_date' => date("Y-m-d H:i:s" , time()),
            'status' => 1,
            'channel' => $channelInfo['channel'],
            'notify_url' => $merch['notify_url'],
            'ip' => Network::GetClientIp(),
            'm_client_ip' => $client_ip ,
            'qr_id' => $qrCode['qr_id'],
            'user_id' => $qrCode['user_id'],
            'qr_pic' => $qrCode['path'],
            'channel_name' => $qrCode['chain_name'],
            'address' => $qrCode['content'],
            'p_real_money' => $p_real_money,
            'agent_real_money' => $agent_real_money ,
            'a_fee' => $agent_channel_fee,
            'agent_id' => $agent_id
        );
        /*echo "<pre>";
        print_r($orderInsert);

        exit;*/
        /*echo "<pre>";
        print_r($orderInsert);

        exit;*/


        $OrdersModel = new OrdersModel();
        $CollectQrcodeModel = new CollectQrcodeModel();
        $object = $this->db(0);
        try{
            $object->beginTransaction();
            $qrInfo = $CollectQrcodeModel->getInfo(['qr_id' => $qrCode['qr_id']] , true );
            $id = $OrdersModel->insertData($orderInsert);
            if( !$id ){
                throw new \Exception( "插入订单表失败"  );
            }
            $update_status = $CollectQrcodeModel->updateData( ['status' => 1,'update_time' => date('Y-m-d H:i:s' ,time())] , ['qr_id' => $qrCode['qr_id']] );
            if( !$update_status ){
                throw new \Exception( "修改二维码表失败..."  );
            }
            $object->Exec("Update collect_qrcode set match_index = match_index - 1 where qr_id = '{$qrCode['qr_id']}' and match_index >= 1  ");
            $object->commit();
            $this->orderStatusWriteQueueScrapy($orderInsert['address'] , $order_sn );
            //把相关匹配的地址和金额写入redis
            $this->saveMatchAddressMoney( $money , $qrCode['content'] );
            return array(
                'order_sn' => $order_sn ,
                'r_money' => $r_money  ,//下单人民币金额
                'money' => $money  ,//需要支付的金额
                'qr_info' => $qrInfo,
            );
        }catch(\Exception $e ){
            //echo $e->getMessage();
            $log_file = Log_file::getInstance(['filename' => 'place_order' ]);
            $log_file->Write("error" , "下单失败，错误信息是：".$e->getMessage() );
            $object->rollBack();
            throw new \Exception( "下单失败，请稍后...."   , 0 );
        }
    }


    //计算汇率

    public function getMoneyRateData( $channel , $place_m_type = 1  ){
        $Source_Exchange = new Source_Exchange();
        $rate =  $Source_Exchange->CnyUSDTHuansuan(1 ,$channel);
        if( empty($rate)){
            throw new \Exception( "没有计算出人民币到usdt的汇率" , 0  );
        }

        $f_rate =  $Source_Exchange->CnyUSDTHuansuan(2 , $channel );
        if( empty($f_rate)){
            throw new \Exception( "没有计算出usdt转人民币的汇率" , 0  );
        }
        $channelModel = new ChannelModel();
        $channelInfo = $channelModel->getInfo(array('channel' => $channel));
        if( empty($channelInfo )){
            throw new Exception(ErrorCode::$noChannelError[API_MSG_NAME],ErrorCode::$noChannelError[API_CODE_NAME]   );
        }

        $extra = $channelInfo['extra'];
        $extraData = @json_decode($extra , true );

        $exchange_change  = isset($extraData['exchange_change']['value']) ? $extraData['exchange_change']['value'] : 0 ;
        $exchange_change = floatval($exchange_change);
        $f_rate = $f_rate + $exchange_change ;

        $rate = Tools::number_format($rate , NUMBER_FORMAT );


        return [
            'rate' => $rate ,
            'f_rate' => $f_rate ,
        ];
    }




    //二维码匹配
    public function matchQrCode($money,$merch = [] , $feeData = []  ){
        //$mid = $merch['mid'];
        //$chain_name = $merch['chain_name'];
        $channel = $feeData['channel'];
        /*$MerchantsBindCollectModel = new MerchantsBindCollectModel();
        $cid = $MerchantsBindCollectModel->getMerchCollect($mid);*/
        //$fen = $money * 100 ;
        $where = " AND a.status = 1 AND a.chain_name = '{$channel}'  AND a.auto_match = 1 and a.active = 1  ";
        //$where .= " AND  cb.status = 1 ";
        $where .= " AND a.content not in ( select address from `orders` as o  where o.status in ( 1 )  and (o.money * 100000000) = ($money * 100000000 ) ) ";
        $sql = "select a.* from collect_qrcode as a   where 1 = 1 {$where} order by a.match_index desc   limit 1";
        
        //$sql = "select * from collect_qrcode as a left join  collect_user as cb on a.user_id = cb.user_id   where 1 = 1 {$where} order by a.match_index desc   limit 1";
        return $this->db(0)->findOne($sql);
    }
    //查询订单
    public function query($params = array() ){
        //检测签名
        $merch = $this->checkSign($params);
        //白名单
        $this->checkWhiteIp($merch);
        $merch_order_sn = isset($params['merch_order_sn']) ? trim($params['merch_order_sn']) :"";
        if( empty($merch_order_sn )){
            throw new Exception("缺失商户订单号" ,ErrorCode::$commonError[API_CODE_NAME]  );
        }
        $OrdersModel = new OrdersModel();
        $order = $OrdersModel->getInfo(array('mid' => $merch['mid'] , 'merch_order_sn' => $merch_order_sn ));
        if( empty($order )){
            throw new Exception("没有查询到订单" ,ErrorCode::$systemError[API_CODE_NAME]  );
        }
        $res = array(
            'merch_order_sn' => $merch_order_sn ,
            'status' =>  intval($order['status']),
        );
        return $res ;
    }
    public function checkOrder($params , $merch ){
        $merch_order_sn = isset($params['merch_order_sn']) ? trim($params['merch_order_sn']) :"";
        $money = isset($params['money']) ? trim($params['money']) :"";
        $channel = isset($params['channel']) ? trim($params['channel']) :"";
        $client_ip = isset($params['client_ip']) ? trim($params['client_ip']) :"";
        $place_m_type = isset($params['place_m_type']) ? trim($params['place_m_type']) :1 ; //金额的单位

        $MerchantFeeModel = new MerchantFeeModel();
        $channelModel = new ChannelModel();
        $OrdersModel = new OrdersModel();
        if(!filter_var($client_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE)) {
            // throw new Exception( "来源IP错误"   ,ErrorCode::$commonError[API_CODE_NAME]  );
        }
        if( $merch['status'] != 1  ){
            throw new Exception(ErrorCode::$merchError[API_MSG_NAME]  ,ErrorCode::$merchError[API_CODE_NAME]  );
        }
        if( empty($merch_order_sn )){
            throw new Exception(ErrorCode::$merchOrderNo[API_MSG_NAME],ErrorCode::$merchOrderNo[API_CODE_NAME]   );
        }

        $stringClass = new StringClass();
        if($stringClass->utf8_str($merch_order_sn) != 1 ){
            throw new Exception(ErrorCode::$merchOrderFormatError[API_MSG_NAME],ErrorCode::$merchOrderFormatError[API_CODE_NAME]   );
        }
        $len = $stringClass->abslength($merch_order_sn);
        if( $len < 6 || $len > 32 ){
            throw new Exception(ErrorCode::$merchOrderFormatError[API_MSG_NAME],ErrorCode::$merchOrderFormatError[API_CODE_NAME]   );
        }
        if( empty( $channel )){
            throw new Exception(ErrorCode::$noChannelError[API_MSG_NAME],ErrorCode::$noChannelError[API_CODE_NAME]   );
        }

        $channelInfo = $channelModel->getInfo(array('channel' => $channel));
        if( empty($channelInfo )){
            throw new Exception(ErrorCode::$noChannelError[API_MSG_NAME],ErrorCode::$noChannelError[API_CODE_NAME]   );
        }
        if( $channelInfo['status'] != 1  ){
            throw new Exception(ErrorCode::$channelCloseError[API_MSG_NAME],ErrorCode::$channelCloseError[API_CODE_NAME]   );
        }

        $feeDatta = $MerchantFeeModel->getInfo(array('mid' =>$merch['mid'] , 'channel' => $channel  ));
        if( empty($feeDatta) ){
            throw new Exception(ErrorCode::$merchFeeError[API_MSG_NAME],ErrorCode::$merchFeeError[API_CODE_NAME]   );
        }

        if($feeDatta['status'] != 1 ){
            throw new Exception(ErrorCode::$merchFeeError[API_MSG_NAME],ErrorCode::$merchFeeError[API_CODE_NAME]   );
        }
        if($feeDatta['fee']  == ''  ){
            throw new Exception(ErrorCode::$merchFeeError[API_MSG_NAME],ErrorCode::$merchFeeError[API_CODE_NAME]   );
        }

        /*if($feeDatta['address']  == ''  ){
            throw new Exception( "商户未配置收款地址 请联系平台客服配置"    );
        }*/
        //金额判断
        if( empty($money) ){
            throw new Exception(ErrorCode::$payMoneyError[API_MSG_NAME],ErrorCode::$payMoneyError[API_CODE_NAME]   );
        }
        $money = floatval($money);
        if( $money * 100  <= 0 ){
            throw new Exception(ErrorCode::$payMoneyError[API_MSG_NAME],ErrorCode::$payMoneyError[API_CODE_NAME]   );
        }
        $fen = $money * 100  ;
        $min_money = $channelInfo['min_money'];
        $max_money = $channelInfo['max_money'];
        //print_r($feeDatta);
        if( $min_money > 0  ){
            if( $fen < $min_money ){
                throw new Exception("支付最低金额必须是:￥" .$min_money /100  ,ErrorCode::$payMoneyError[API_CODE_NAME]   );
            }
        }
        if( $max_money > 0  ){
            if( $fen > $max_money ){
                throw new Exception("支付最大金额是:￥" .$max_money /100  ,ErrorCode::$payMoneyError[API_CODE_NAME]   );
            }
        }


        $ordrInfo = $OrdersModel->getInfo( array('merch_order_sn' => $merch_order_sn ,'mid' => $merch['mid']) );
        if( !empty($ordrInfo) ){
            throw new Exception(ErrorCode::$payMerchOrderSameError[API_MSG_NAME] ,ErrorCode::$payMerchOrderSameError[API_CODE_NAME]   );
        }
        return array(
            'channelInfo'=>$channelInfo,
            'feeData'=>$feeDatta,
        );
    }

    /**
     * 代理商通道检测
     * $agent_id	int  代理商的id
     * $channelInfo	array 商户通道信息
     * $data array 返回的数组
     */

    public function checkAgentChannel( $agent_id = 0 , $channelInfo = []  ){
        if($agent_id <= 0 ){
            return true ;
        }
        $AgentFeeModel = new AgentFeeModel();
        $agentChannel = $AgentFeeModel->getAgentHasChannel($agent_id ,$channelInfo['channel'] );
        if( empty($agentChannel )){
            Log_Db::WriteLog("placeOrder" , "代理商费率没有 , 代理商的id是：{$agent_id} 通道是:{$channelInfo['channel']}" , "error");
            throw new Exception(ErrorCode::$payOrderError[API_MSG_NAME]  ,ErrorCode::$payOrderError[API_CODE_NAME]  );
        }
        if($agentChannel['status'] != 1 ){
            Log_Db::WriteLog("placeOrder" , "代理商费率错误 , 代理商的id是：{$agent_id} 通道是:{$channelInfo['channel']} ，没开启通道" , "error");
            throw new Exception(ErrorCode::$payOrderError[API_MSG_NAME]  ,ErrorCode::$payOrderError[API_CODE_NAME]  );
        }
        return $agentChannel ;
    }
    //检测签名,如果成功返回商户数据
    public function checkSign($params){
        $Source_Merch = new Source_Merch();
        return $Source_Merch->checkSign($params);
    }
    //白名单检测
    public function checkWhiteIp( $merch = []  ){

    }

    //检测商户
    public function checkMerch( $merch = [] ){
        if( empty($merch['notify_url'] )){
            throw new Exception("商户暂无配置异步回调地址 请去商户后台配置");
        }
        if( empty($merch['supplement_url'] )){
            throw new Exception("商户暂无配置补单异步回调地址 请去商户后台配置");
        }
    }


    /**
     * Trc20 币收到到账消息处理业务逻辑
     * $address	string 币地址
     * $money 	string 支付金额
     * txid    string 区块链的交易id
     */

    public function do_success_trc20_order($data = [] ){
        $this->checkRsyncWhiteIp();
        $Event_name =  isset($data['Event_name']) ? $data['Event_name'] :"";
        $transaction_id = $transaction =  isset($data['transaction']) ? $data['transaction'] :"";
        $from =  isset($data['from']) ? $data['from']:"";
        $to =  isset($data['to']) ? $data['to']:"";
        $value =  isset($data['value']) ? $data['value']: 0 ;
        if( empty($from ) or empty($to) or empty($value)){
            throw  new \Exception("参数错误");
        }
        $Tron_Address_Address = new Tron_Address_Address();
        //$from =   $Tron_Address_Address->hexString2Address( $from );
        //$to =   $Tron_Address_Address->hexString2Address( $to );
        //$money = Tools::number_format( $value / (pow( 10 , 6 )) ) ;//合约是6位
        $money = $value ;
        if($Event_name != 'Transfer'){
            throw  new \Exception("不是转账的， 消息通知 不允许");
        }
        //判断到账的那个地址是否是系统的
        $CollectQrcodeModel = new CollectQrcodeModel();
        $OrdersPaymentLogModel = new OrdersPaymentLogModel();
        $OrdersModel = new OrdersModel();
        $qrInfo = $CollectQrcodeModel->getInfo(['content' => $to ]) ;
        if( empty($qrInfo )){
            throw  new \Exception("收款地址：{$to} , 不合法 ， 不是系统的  ");
        }
        $tansInfo = $OrdersPaymentLogModel->getPaymentBytransaction_id($transaction);
        if( $tansInfo ){
            throw  new \Exception("此交易信息已经存在：" . $transaction);
        }
        $matchOrder = $this->findOrder( $to  , $money);

        $match_order_sn = isset($matchOrder['order_sn']) ? $matchOrder['order_sn'] :"";
        $paymentInsert = [
            'money' => $money ,
            'create_date' => date("Y-m-d H:i:s" , time() ),
            'extra' => json_encode($data , JSON_UNESCAPED_UNICODE) ,
            'message' => "收到到账消息通知",
            'to_address' => $to ,
            'from_address' => $from ,
            'transaction_id' => $transaction,
            'client_ip' => Network::GetClientIp(),
            'match_order' => json_encode($matchOrder , JSON_UNESCAPED_UNICODE) ,
        ];

        if( $match_order_sn ){
            $paymentInsert['order_sn'] = $match_order_sn ;
            $orderInfo = $OrdersModel->getInfo( ['order_sn' => $match_order_sn]);
            $user_id = isset($orderInfo['user_id']) ? $orderInfo['user_id'] : 0 ;
            $paymentInsert['user_id'] = $user_id ;
            $stansInfo = $OrdersPaymentLogModel->getPaymentByOrderSn($match_order_sn);
            if( $stansInfo ){
                throw  new \Exception("此订单信息已经存在：订单号： " . $match_order_sn);
            }
            $this->notify($match_order_sn ,$transaction_id , 2  );
        }
        $OrdersPaymentLogModel->insertData($paymentInsert);
        return  true ;
    }

    /**
     * 检测回调订单的白名单IP
     */
    public function checkRsyncWhiteIp(){
        $ip = Network::GetClientIp();
        $rsync_white_ips = Source_Sysconfig::getInstance()->getVal('rsync_white_ip');
        if( empty($rsync_white_ips)){
            throw  new \Exception("系统暂无配置白名单IP");
        }
        $rsync_white_ip = explode("\n" ,$rsync_white_ips );
        $hash = [] ;
        foreach ($rsync_white_ip as $val ){
            if( !empty($val )){
                $hash[] = trim($val) ;
            }
        }
        if( in_array($ip , $hash )){
            return true ;
        }
        throw  new \Exception("ip:{$ip} 不在白名单：{$rsync_white_ips} ");
    }

    /**
     * 根据币地址 和 支付的金额 去处理订单支付成功的逻辑
     * $address	string 币地址
     * $money 	string 支付金额
     * txid    string 区块链的交易id
     */
    public function do_success_order($address = '' , $money = '',$txid = ''){
        $orderMatch = $this->findOrder( $address , $money );
        $order_sn = isset($orderMatch['order_sn']) ? $orderMatch['order_sn'] :"";
        $match = isset($orderMatch['match']) ? $orderMatch['match'] :"";
        if( empty( $order_sn  )){
            Log_Db::WriteLog("do_success_order" , "系统没有匹配到待付款的订单数据根据地址：{$address} ， 和金额：{$money} ,匹配到的数据是：" . var_export($orderMatch , true ));
            throw new Exception("系统没有匹配到待付款的订单数据根据地址：{$address} ， 和金额：{$money} ");
        }
        $this->notify($order_sn,$txid , 2 );
    }


    /**
     * 根据币地址 和 支付的金额 去找未支付的订单
     * $address	string 币地址
     * $money 	string 支付金额

     */
    public function findOrder($address = '' , $money =  0   ){
        if( empty($address)  or empty($money) ){
            return [] ;
        }
        $sql = "select a.*   from `orders` as a  
        where a.status = 1 and a.address = '{$address}' ";
        $result = $this->db(0  )->find($sql);
        if( empty($result)  ){
            return [] ;
        }
        $order_sn = '' ;
        $match = [] ;

        foreach ($result as $key => $val ){
            /*echo $val['money'] * NUMBER_FORMAT;
            echo "<h1>";
            echo $money * NUMBER_FORMAT ;
            die();*/
            if($val['money'] * (pow(10 , NUMBER_FORMAT)) == $money * pow(10 , NUMBER_FORMAT) ){
                $create_date =  strtotime($val['create_date']);
                if( time() - $create_date > 3600 ){
                    //如果超过了一定的时间
                    continue ;
                }
                $match[] = $val ;
            }
        }
        if( empty( $match )){
            return [] ;
        }
        if( count($match) == 1 ){
            $order_sn = $match[0]['order_sn'];
        }
        $hash = [
            'order_sn' => $order_sn ,
            'match' => $match,
        ];
        return $hash ;
    }

    /**
     * 异步回调 做核心的业务处理， 包含了订单的成功和失败的核心逻辑
     * $order_sn	string 系统订单号（我们系统的订单号）
     * $txid	string 交易单号区块链的交易id
     * $action	int 2:成功 3：失败
     * $out_order_sn	string 外部上游的订单号
     * $data array 返回的数组
     */
    public function notify($order_sn = ''  , $txid = ''    , $action = 2  , $out_order_sn = '' ){
        if( empty($order_sn)   ){
            throw new Exception("缺失系统单号");
        }
        $orderInfo = [] ;
        $ordersModel = new OrdersModel();
        $MerchantModel = new MerchantModel();
        $MerchantMoneyLogModel =  new MerchantMoneyLogModel();
        $OrdersSupplementsModel = new OrdersSupplementsModel();
        $CollectQrcodeModel = new CollectQrcodeModel();
        $Source_Orders = new Source_Orders();
        $AgentModel = new AgentModel();
        $AgentMoneyLogModel = new AgentMoneyLogModel();
        if( !in_array($action , array(2,3) )){
            throw new Exception("参数错误");
        }
        $object = $this->db(0);
        try{
            $now_date = date("Y-m-d H:i:s" ,time() ) ;
            $object->beginTransaction();
            $orderInfo = $ordersModel->getInfo( array('order_sn' => $order_sn ) , true  );
            if( empty($orderInfo)){
                throw new Exception( "订单不存在"  );
            }
            if( $orderInfo['status'] != 1   ){
                throw new Exception( "订单的状态不是待付款，无法操作"  );
            }
            $mid = $orderInfo['mid'];
            $qr_id = $orderInfo['qr_id'];
            $agent_id = $orderInfo['agent_id'];//代理商的id
            $qr_info = $CollectQrcodeModel->getInfo(['qr_id' => $qr_id] , true );
            //查询商户的表
            $merchInfo = $MerchantModel->getInfo(array('mid' => $mid) , true  );
            if( empty($merchInfo) ){
                throw new Exception( "没有查询到商户的信息" );
            }
            $agentInfo = [] ;
            if($agent_id > 0 ){
                $agentInfo = $AgentModel->getInfo(['agent_id' => $agent_id], true );
            }


            //修改订单数据表
            $updateOrder = array(
                'status' =>  $action  ,
                'complete_date' => date("Y-m-d H:i:s" ,time() ) ,
            );
            if( $action == 3 ){
                unset($updateOrder['complete_date']);
            }
            /*if($out_order_sn){
                $updateOrder['out_order_sn'] = $out_order_sn ;
            }*/
            if($txid){
                $updateOrder['txid'] = $txid ;
            }
            $updateStatus = $ordersModel->updateData($updateOrder,array('order_sn' => $order_sn )  );
            if( !$updateStatus ){
                throw new Exception( "修改订单数据失败" );
            }
            if($action == 2 ){//订单支付成功
                //修改订单状态
                //商户加钱
                //给商户加钱
                $real_money = $orderInfo['real_money'];
                $now_money =  $merchInfo['money'] + $real_money ;
                $sql = "update merchants set money = $now_money , update_time = '{$now_date}'  where mid = '{$mid}'  ";
                $update = $object->Exec($sql);
                if( !$update ){
                    throw new Exception( "修改商户的金额出错" );
                }
                //插入资金日志
                $logInsert = array(
                    'mid'=>$mid ,
                    'money' => $real_money ,//变动的资金
                    'account_money' => $merchInfo['money'],
                    'now_money' => $now_money,
                    'type'=> 3,
                    'create_time' => $now_date,
                    'update_time' => $now_date,
                    'remark' => "订单付款确认, 返还商户:".(Common::foramtNumber($real_money, NUMBER_FORMAT )),
                );
                $Log_id = $MerchantMoneyLogModel->insertData($logInsert);
                if(!$Log_id ){
                    throw new Exception( "保存商户资金日志数据失败" );
                }
                if($agent_id > 0 ){
                    //代理加钱
                    //给代理加钱
                    $agent_real_money = $orderInfo['agent_real_money'];//实际到代理商多少钱
                    $agent_now_money =  $agentInfo['money'] + $agent_real_money ;
                    $sql = "update agent set money = $agent_now_money , update_time = '{$now_date}'  where agent_id = '{$agent_id}'  ";
                    $update = $object->Exec($sql);
                    if( !$update ){
                        throw new Exception( "修改代理商的金额出错" );
                    }
                    //插入资金日志
                    $logInsert = array(
                        'agent_id'=>$agent_id ,
                        'money' => $agent_real_money ,//变动的资金
                        'account_money' => $agentInfo['money'],
                        'now_money' => $agent_now_money,
                        'type'=> 3,
                        'create_time' => $now_date,
                        'update_time' => $now_date,
                        'remark' => "订单付款确认, 返还代理商:".$agent_real_money,
                    );
                    $Log_id = $AgentMoneyLogModel->insertData($logInsert);
                    if(!$Log_id ){
                        throw new Exception( "保存代理商资金日志数据失败" );
                    }
                }
                $CollectQrcodeModel->updateData( ['status' => 1,'update_time' => date("Y-m-d H:i:s")  ] , ['qr_id' => $qr_id] );
                //Log_Db::WriteLog("order_success_update_qr" , "订单确认成功 修改二维码为成功，继续下一次接单，订单编号：{$order_sn} ,二维码id：{$qr_id}，当前的二维码状态是：{$qr_info['status']}");
                //$Source_Orders->orderSuccessStatis($order_sn);
            }
            $this->db(0)->Exec("update collect_qrcode set match_index = match_index - 1 where match_index > 0 and qr_id = '{$qr_id}' ");
            if($action == 3 ){//失败订单
                $CollectQrcodeModel->updateData( ['status' => 1,'update_time' => date("Y-m-d H:i:s")  ] , ['qr_id' => $qr_id  ] );
                //Log_Db::WriteLog("order_error_update_qr" , "订单确认成功 准备修改二维码为正常，订单编号：{$order_sn} ,二维码id：{$qr_id}，当前的二维码状态是：{$qr_info['status']}");
                // $Source_Orders->orderErrorStatis($order_sn);
            }
            $object->commit();
            if($action == 2 ){//如果操作的是订单支付成功 那么给商户通知
                $this->notifyMerchWriteQueue($order_sn);
            }
            return true ;
        }catch(Exception $e ){
            $object->rollBack();
            throw new Exception( "确认订单操作失败：" . $e->getMessage() );
        }
    }
    /**
     * 通知商户 写入队列处理
     * $order_sn	string 系统订单号
     */
    public function notifyMerchWriteQueue($order_sn){
        global $_G ;
        require_once  APP_PATH . '/application/library/Beanstalk/vendor/autoload.php' ;
        $host = $_G['config']['beanstald']['host'];
        $port = $_G['config']['beanstald']['port'];
        $pheanstalk = new Pheanstalk($host  , $port);
        return $pheanstalk
            ->useTube('rsync_merch_orders')
            ->put($order_sn);
    }

    /**
     * 把单信息写入队列 主要是为了去爬虫订单的状态
     * $address 	string  收款地址
     */
    public function orderStatusWriteQueueScrapy($address , $order_sn = '' ){
        global $_G ;
        require_once  APP_PATH . '/application/library/Beanstalk/vendor/autoload.php' ;
        $host = $_G['config']['beanstald']['host'];
        $port = $_G['config']['beanstald']['port'];
        $pheanstalk = new Pheanstalk($host  , $port);
        return $pheanstalk
            ->useTube('order_pay_status_scrapy')
            ->put( json_encode( ['address' => $address , 'order_sn' => $order_sn ] )  , 0 , 10 );
    }
        /**
     * 控制接口的某个操作的访问频率
     * $mid 商户id
     * $action 操作
     */
    public function checkVisit($mid  = 0 , $action = 'place_order',$limit = 10  ){
        $redisHandler = RedisClass::getInstance(10)->handler;
        $redis_key = "user:{$mid}:$action";
        $check = $redisHandler->exists($redis_key);
        if($check){
            $count = $redisHandler->get($redis_key);
            if($count > $limit){
                throw new Exception('your have too many request please wait ');
            }
            $redisHandler->incr($redis_key);  //键值递增
        }else{
            $redisHandler->incr($redis_key);
            //限制时间为60秒
            $redisHandler->expire($redis_key,60);
        }
    }
    //地址和金额写入redis
    public function saveMatchAddressMoney( $money , $address ){
        $redisHandler = RedisClass::getInstance(11);
        $str = (string)($money *  pow(10 , NUMBER_FORMAT)) ; 
        $redis_key = "m:" . $address . ":" .  $str ;
        return $redisHandler->SET( $redis_key , 1 , 180 );
    }
    //判断地址和金额
    //如果满足那么返回true 不满足返回false
    public function checkMatchAddressMoneyAllow( $money , $address ){
        $redisHandler = RedisClass::getInstance(11);
        $str = (string)($money *  pow(10 , NUMBER_FORMAT)) ;
        $redis_key = "m:" . $address . ":" . $str  ;
        $value =  $redisHandler->GET( $redis_key  );
        return ($value == 1) ? false  : true ;
    }
}