<?php
/**
 * Created by 不要复制我的代码.
 * User: 不要复制我的代码
 * Date: 2019/5/14 0014
 * Time: 上午 11:45
 */
class OrdersPaymentLogModel extends AbstractModel{
    protected $table = "orders_payment_log" ;
    protected $dbIndex = 0  ;

    //根据交易id获取数据
    public function getPaymentBytransaction_id($transaction_id = '' ){
        return $this->getInfo( ['transaction_id' => $transaction_id] );
    }

    public function getPaymentByOrderSn($order_sn = '' ){
        return $this->getInfo( ['order_sn' => $order_sn] );
    }
}