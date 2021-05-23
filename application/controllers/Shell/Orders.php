<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/23 0023
 * Time: 20:58
 */
class Shell_OrdersController extends Shell_CommonController
{
    public function init()
    {
        parent::init();
    }
    //异步通知商户订单
    public function notifyMerchAction(){
        $Business = Common::ImportBusiness("OrdersShell" ,"Shell" );
        $Business->notifyMerch();
    }
    //检测是否支付
    public function checkPayAction(){
        $Business = Common::ImportBusiness("OrdersShell" ,"Shell" );
        $Business->checkPay();
    }

    //订单过期检测
    public function expireAction(){
        $Business = Common::ImportBusiness("OrdersShell" ,"Shell" );
        $Business->expire();
    }

}