<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Merch_TestController extends Merch_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        $ChannelModel = new ChannelModel();
        $allChannel = $ChannelModel->getChannel( " AND status = 1 ");
        $res = array(
            'pageSize'=> 10 ,
            'allChannel' => $allChannel ,
        );

        $this->displayTemplate('test/place_order' , $res);
    }



    public function placeOrderAction(){
        $MerchantModel = new MerchantModel();
        $merchInfo = $MerchantModel->getInfo( array('mid' => $this->userInfo['mid']  )  );
        $params = $this->getParams();
        $money = isset($params['money']) ? $params['money'] :"";
        if( empty($money )){
            exit("金额错误");
        }
        $channel = isset($params['channel']) ? $params['channel'] :"";
        $req = [
            'appid' =>$merchInfo['appid'],
            'merch_order_sn' =>Tools::build_order_no(16),
            'channel' =>$channel,
            'money' =>$money,
            'client_ip' =>'127.0.0.1',
            'sign' => strtolower(md5($merchInfo['appid'] .$merchInfo['appsecret']) ) ,
            'place_m_type' => isset($params['place_m_type']) ? $params['place_m_type'] :"1",
        ];
        
        
        $Source_Merch = new Source_Merch();
        $sign = $Source_Merch->getSignature($req ,$merchInfo['appsecret'] );
        $req['sign'] = $sign ;

        global $_G;
        $Business = Common::ImportBusiness("Orders" ,"Api" );
        try{
            
            $data = $Business->placeOrder($req);
            //跳转到支付页面
            $order_sn = $data['order_sn'];
            $url = $_G['config']['domain']['domain']['API_DOMAIN'] . "Api_Pay/pay?order_sn=".$order_sn;
            $resp = [
                'order_sn' => $order_sn,
                'pay_url' => $url
            ];
            header("Location:" . $url);
            exit;
            Common::EchoResult(1 , "下单成功" , $resp);
        }catch(Exception $e ){
            Common::EchoResult($e->getCode(),$e->getMessage());
        }
    }
}