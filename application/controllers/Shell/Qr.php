<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/23 0023
 * Time: 20:58
 */
class Shell_QrController extends Shell_CommonController
{
    public function init()
    {
        parent::init();
    }
    //处理批量导入的二维码
    /*public function qrAction(){
        $Business = Common::ImportBusiness("Qr" ,"Shell" );
        $Business->doQr();
    }*/
    //凌晨的时候把所有的码标记为禁用
    /*public function deleteQrAction(){
        $Business = Common::ImportBusiness("Qr" ,"Shell" );
        $Business->deleteQr();
    }*/

    //资金同步
    /*public function moneyAction(){
        $Business = Common::ImportBusiness("Qr" ,"Shell" );
        $Business->money();
    }*/

    //把二维码地址写入redis
    public function addressToRedisAction(){
        $Business = Common::ImportBusiness("Qr" ,"Shell" );
        $Business->addressToRedis();
    }
    //匹配指数重置
    public function resetIndexAction(){
        $Business = Common::ImportBusiness("Qr" ,"Shell" );
        $Business->resetIndex();
    }
}