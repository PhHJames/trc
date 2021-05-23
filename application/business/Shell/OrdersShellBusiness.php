<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/23 0023
 * Time: 21:03
 */
use Pheanstalk\Pheanstalk;
class OrdersShellBusiness extends AbstractModel {
    public $max_notify_number =   4   ;//给商户通知的最大次数

    public $max_check_order_num = 50 ;
    //异步通知商户 订单的状态
    //只做通知
    public function notifyMerch(){
        $pheanstalk = $this->getBeanstaldObj();
        $Source_Merch = new Source_Merch();
        while(1){
            try{
                $job = $pheanstalk
                    ->watch('rsync_merch_orders')
                    ->ignore('default')
                    ->reserve( 5 );
                if( empty( $job )){
                    throw new Exception("没有获取到数据");
                }
                $data =  $job->getData();
                if( empty($data )){
                    throw new Exception("没有获取到数据");
                }
                $order_sn = $data ;
                $jobInfo = $pheanstalk->statsJob($job);
                $jobInfo = json_encode($jobInfo);
                $jobInfo = json_decode($jobInfo , true );
                $reserves = isset($jobInfo['reserves']) ? intval($jobInfo['reserves']) : 0 ;//已经拿队列的次数
                echo "【". date("Y-m-d H:i:s" , time() ). "】:开始执行，订单号是：{$order_sn} \n";
                if( $reserves >= $this->max_notify_number ){
                    $pheanstalk->delete($job);//删除队列
                    echo "【". date("Y-m-d H:i:s" , time() ). "】: 已经拿的次数是：{$reserves}，最大次数是:{$this->max_notify_number} 订单号是：{$order_sn} \n";
                    continue ;
                }
                $Source_Merch->sendRsyncOrderReq($order_sn , function($ret) use ($pheanstalk , $job ,$reserves , $order_sn    ) {
                    if(strtolower($ret) == 'success' ){
                        $rsync_status = 2 ;
                        $pheanstalk->delete($job);//删除队列
                        echo "【". date("Y-m-d H:i:s" , time() ). "】:商户响应成功，订单号是：{$order_sn} \n";
                    }else{
                        $pheanstalk->release( $job , 0 , $reserves * 20 );//继续延迟执行
                        echo "【". date("Y-m-d H:i:s" , time() ). "】:队列延迟执行，订单号是：{$order_sn} \n";
                        $rsync_status = 3 ;
                    }
                    $ret = addslashes($ret);
                    $sql = " update orders set rsync_status = '{$rsync_status}' , rsync_message = '{$ret}' , notify_num = notify_num + 1 where order_sn = '{$order_sn}'  ";
                    $this->db(0)->Exec( $sql );
                });

            }catch( Exception $e ){
                echo "【". date("Y-m-d H:i:s" , time() ). "】:异步通知商户：" . $e->getMessage() . "\n";
                continue ;
            }
        }
    }




    public function getBeanstaldObj(){
        global $_G ;
        require_once  APP_PATH . '/application/library/Beanstalk/vendor/autoload.php' ;
        $host = $_G['config']['beanstald']['host'];
        $port = $_G['config']['beanstald']['port'];
        $pheanstalk = new Pheanstalk($host  , $port);
        return $pheanstalk ;
    }

    public function expire(){
        $order_expire =  intval(Source_Sysconfig::getInstance()->getVal('order_expire'));
        $end = time()  -  $order_expire;
        $sql = "select * from orders where status = 1 and UNIX_TIMESTAMP(create_date)   < '{$end}' ";
        $list = $this->db(0)->find($sql);
        if( empty($list )){
            echo "【". date("Y-m-d H:i:s" , time() ). "】:没有过期的订单："  . "\n";
            return ;
        }
        $api_business = Common::ImportBusiness("Orders" , "Api");
        $Source_Orders = new Source_Orders();
        foreach ($list as $kk => $vv ){
            try{
                $api_business->notify($vv['order_sn'] , '' , 3 );
                echo "【". date("Y-m-d H:i:s" , time() ). "】:处理订单失败成功：订单id：{$vv['order_sn']} " .  "\n";
            }catch( \Exception $e ){
                echo "【". date("Y-m-d H:i:s" , time() ). "】:处理订单失败错误：订单id：{$vv['order_sn']} "  .$e->getMessage() .  "\n";
            }
        }
        echo "【". date("Y-m-d H:i:s" , time() ). "】:success" .  "\n";
    }



    //检测是否支付
    public function checkPay(){
        $pheanstalk = $this->getBeanstaldObj();
        $API_Business = Common::ImportBusiness("Orders" , "Api");
        $Source_USTDTRC = new Source_USTDTRC();
        while(1){
            try{
                $job = $pheanstalk
                    ->watch('order_pay_status_scrapy')
                    ->ignore('default')
                    ->reserve( 5 );
                if( empty( $job )){
                    throw new Exception("没有获取到数据");
                }
                $data =  $job->getData();
                if( empty($data )){
                    throw new Exception("没有获取到数据");
                }
                $data = json_decode( $data , true );
                $OrdersModel = new OrdersModel();
                $CollectQrcodeModel = new CollectQrcodeModel();
                $OrdersPaymentLogModel = new OrdersPaymentLogModel();
                $address =  isset($data['address']) ? $data['address'] : ''  ;
                $order_sn =  isset($data['order_sn']) ? $data['order_sn'] : ''  ;
                $jobInfo = $pheanstalk->statsJob($job);
                $jobInfo = json_encode($jobInfo);
                $jobInfo = json_decode($jobInfo , true );
                $reserves = isset($jobInfo['reserves']) ? intval($jobInfo['reserves']) : 0 ;//已经拿队列的次数

                if( empty($address)){
                    $pheanstalk->delete($job);//删除队列
                    echo "【". date("Y-m-d H:i:s" , time() ). "】: 收款地址是空 删除此队列 \n";
                    continue ;
                }

                echo "【". date("Y-m-d H:i:s" , time() ). "】:开始执行，收款地址是：{$address} \n";

                $orderInfo = $OrdersModel->getInfo(['order_sn' => $order_sn]);
                if( empty($orderInfo)){
                    $pheanstalk->delete($job);//删除队列
                    echo "【". date("Y-m-d H:i:s" , time() ). "】: 订单号是：{$order_sn} 没有获取到订单信息 \n";
                    continue ;
                }
                if( $orderInfo['status'] != 1  ){
                    $pheanstalk->delete($job);//删除队列
                    echo "【". date("Y-m-d H:i:s" , time() ). "】: 订单号是：{$order_sn} 不是待付款状态 删除队列数据 \n";
                    continue ;
                }
                $qr_id = $orderInfo['qr_id'];

                $qrInfo = $CollectQrcodeModel->getInfo(['qr_id' => $qr_id]);
                if( empty($qrInfo)){
                    $pheanstalk->delete($job);//删除队列
                    echo "【". date("Y-m-d H:i:s" , time() ). "】: 订单号是：{$order_sn} ， 二维码id是：{$qr_id}  没有获取到二维码信息 \n";
                    continue ;
                }
                /*if( $reserves >= $this->max_check_order_num ){
                    $pheanstalk->delete($job);//删除队列
                    echo "【". date("Y-m-d H:i:s" , time() ). "】: 已经拿的次数是：{$reserves}，最大次数是:{$this->max_check_order_num} 订单号是：{$order_sn} \n";
                    continue ;
                }*/
                $transList = $Source_USTDTRC->getTrcTransactionsListByAddress($address , 10 );
                $transList = isset($transList['data'])?$transList['data']:[];
                if( empty($transList) ){
                    echo "【". date("Y-m-d H:i:s" , time() ). "】:  收款地址是：{$address}   没有获取到交易的相关信息，队列延迟执行 等待下一次爬取 \n";
                    $pheanstalk->release( $job , 0 ,  3  );//继续延迟执行
                    continue ;
                }
                foreach ($transList as $tkey => $tval ){
                    $transaction_id = isset($tval['transaction_id']) ? $tval['transaction_id'] :"";
                    $from = isset($tval['from']) ? $tval['from'] :"";
                    $to = isset($tval['to']) ? $tval['to'] :"";
                    $value = isset($tval['value']) ? $tval['value'] :"";
                    $token_info = isset($tval['token_info']) ? $tval['token_info'] : [] ;
                    $decimals = isset($token_info['decimals']) ? $token_info['decimals'] : 0 ;
                    $symbol = isset($token_info['symbol']) ? $token_info['symbol'] : ""  ;
                    $concat_address = isset($token_info['address']) ? $token_info['address'] : ""  ;

                    $tansInfo = $OrdersPaymentLogModel->getPaymentBytransaction_id($transaction_id);
                    if( $tansInfo ){
                        echo "【". date("Y-m-d H:i:s" , time() ). "】:  收款地址是：{$address}   ,交易id是：{$transaction_id} ，已经存在 \n";
                        continue ;
                    }
                    if( $symbol != 'USDT'){
                        echo "【". date("Y-m-d H:i:s" , time() ). "】:  收款地址是：{$address}   ,交易id是：{$transaction_id} ，类型是：{$symbol} 不合法\n";
                        continue ;
                    }
                    if( $concat_address != 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t'){
                        echo "【". date("Y-m-d H:i:s" , time() ). "】:  收款地址是：{$address}   ,交易id是：{$transaction_id} ，类型是：{$symbol} ,合约地址是：{$concat_address} , 不合法\n";
                        continue ;
                    }
                    $money = Tools::number_format($value / pow( 10 , $decimals) , NUMBER_FORMAT ) ;
                    $matchOrder = $API_Business->findOrder( $address  , $money);
                    $match_order_sn = isset($matchOrder['order_sn']) ? $matchOrder['order_sn'] :"";
                    $paymentInsert = [
                        'money' => $money ,
                        'create_date' => date("Y-m-d H:i:s" , time() ),
                        'extra' => json_encode($tval , JSON_UNESCAPED_UNICODE) ,//到账的消息
                        'message' => "收到到账消息通知",
                        'to_address' => $to ,
                        'from_address' => $from ,
                        'transaction_id' => $transaction_id,
                        'match_order' => json_encode($matchOrder , JSON_UNESCAPED_UNICODE) ,
                    ];
                    if( $match_order_sn ){
                        $paymentInsert['order_sn'] = $match_order_sn ;
                        $orderInfo = $OrdersModel->getInfo( ['order_sn' => $match_order_sn]);
                        $user_id = isset($orderInfo['user_id']) ? $orderInfo['user_id'] : 0 ;
                        $paymentInsert['user_id'] = $user_id ;
                        $stansInfo = $OrdersPaymentLogModel->getPaymentByOrderSn($match_order_sn);
                        if( $stansInfo ){
                            continue ;
                        }
                        $API_Business->notify($match_order_sn ,$transaction_id , 2  );
                    }
                    $OrdersPaymentLogModel->insertData($paymentInsert);
                }
                $pheanstalk->release( $job , 0 ,  3  );//继续延迟执行
                echo "【". date("Y-m-d H:i:s" , time() ). "】:队列延迟执行，收款地址是：{$address} \n";

            }catch( Exception $e ){
                echo "【". date("Y-m-d H:i:s" , time() ). "】:检测订单支付状态：" . $e->getMessage() . "\n";
                continue ;
            }
        }
    }
}