<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/19 0019
 * Time: 21:13
 */
class Api_PayController extends Api_CommonController
{
    public function init(){
        parent::init();
    }
    //下单
    public function payAction(){
        $params = $this->getParams();
        $order_sn = isset($params['order_sn']) ? trim($params['order_sn']) :"";

        $OrdersModel = new OrdersModel();
        $CollectQrcodeModel = new CollectQrcodeModel();
        $info = $OrdersModel->getInfo(['order_sn' =>$order_sn ]);
        if( empty($info)){
            exit("订单不存在");
        }
        if($info['status'] != 1 ){
            exit("必须是待付款订单 ！！");
        }

        $info['money'] = Common::foramtNumber($info['money'] , NUMBER_FORMAT , 1  )  ;
        $info['f_rate'] = Common::foramtNumber($info['f_rate'] , NUMBER_FORMAT , 1  )  ;
        $order_expire = Source_Sysconfig::getInstance()->getVal('order_expire');
        $maxtime = ($order_expire );
        $expire_date = date("H:i" , $maxtime );
        $qr_info = $CollectQrcodeModel->getInfo(['qr_id' => $info['qr_id']]);
        $isAliClient = $this->isAliClient();
        global $_G;
        $qr_pic = $_G['config']['domain']['domain']['UPLOADS_HOST'] . $info['qr_pic'];
        $res = [
            'info' => $info ,
            'expire_date' => $expire_date,
            'maxtime' => $maxtime,
            'isAliClient' => $isAliClient,
            'qr_info' => $qr_info,
            'qr_pic' => $qr_pic
        ];
        $file = "pay";

        /*if($isAliClient == 1 ){
            $file = "zfb";
        }*/
        $this->displayTemplate($file , $res );
        exit;
    }

    //查询订单
    public function queryAction(){
        $params = $this->getParams(1) ;
        $order_sn = isset($params['order_sn'])?$params['order_sn']:"";
        if( empty($order_sn )){
            $this->echoJson(  0  ,"缺失订单号" );
        }
        $OrdersModel = new OrdersModel();
        $orderInfo = $OrdersModel->getInfo(['order_sn' => $order_sn]);
        if( empty($orderInfo )){
            $this->echoJson(  0  ,"没有查询到订单" );
        }
        $status = isset($orderInfo['status']) ? $orderInfo['status'] : '' ;
        $this->echoJson( 1, "查询成功" , ['status' => $status] );

    }

    /**
     * 判断是否支付宝内置浏览器访问
     * @return bool
     */
    function isAliClient()
    {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'Alipay') !== false;
    }
}