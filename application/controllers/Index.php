<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2020/7/10 0010
 * Time: 18:14
 */
class IndexController extends Merch_CommonController {
    public function init() {
        parent::init();
    }
    public function indexAction(){
        exit("welcome");
        $hash = [];
        $channelModel = new ChannelModel();

        $channelInfo = $channelModel->getInfo(array('channel' => 'USDT_TRC20'));
        $min = $channelInfo['min_money'];
        for( $k = 1 ;$k<13;$k++){
            $money = rand($min,$min + 100 );
            $y_price  = Common::foramtNumber( ( ($money + 10000) / 100 ) ,2) ;
            $money = Common::foramtNumber( $money / 100 ,2);
            $pic = "/resource/static/images/goods/{$k}.jpg";
            if( !file_exists(APP_PATH . "/public/resource/static/images/goods/{$k}.jpg" ) ){
                $pic = "/resource/static/images/goods/".rand(1,6).".jpg";
            }
            $goods[] = [
                'money' => $money ,
                'y_price' => $y_price,
                'title' => "廉价商品-" . $k,
                'click' => rand(100 , 999 ),
                'pic' => $pic,
            ];
        }

        $res = [
            'goods' => $goods ,
        ];
        //print_r($goods);

        $this->displayTemplate("goods/index",$res);
    }

    //生成下单的页面地址
    private function jumpAction(){
        $params = $this->getParams();
        $money = isset($params['money']) ? $params['money'] : 0 ;
        if( empty($money )){
            exit("金额错误");
        }
        $account = 'test001';
        $MerchantModel = new MerchantModel();
        $merchInfo = $MerchantModel->getInfo(['account' => $account]);
        if( empty($merchInfo )){
            exit("错误了，，，，，，");
        }
        $channel = 'USDT_TRC20';
        $req = [
            'appid' =>$merchInfo['appid'],
            'merch_order_sn' =>Tools::build_order_no(16),
            'channel' => $channel,
            'money' =>$money,
            'client_ip' =>'127.0.0.1',
            'sign' => strtolower(md5($merchInfo['appid'] .$merchInfo['appsecret']) ) ,
        ];
        $Business = Common::ImportBusiness("Orders" ,"Api" );

        global $_G;
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
        }catch(Exception $e ){
            exit($e->getMessage());
        }
    }

    public function testAction(){
        $Source_USTDTRC = new Source_USTDTRC();
        $tt = $Source_USTDTRC->getAccountMoney('TAqF1BphciVbxzMR2qJyWdBWRFzz6xj2nh');
        print_r($tt);
    }
}