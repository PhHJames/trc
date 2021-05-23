<?php  
class Admin_IndexController extends Admin_CenterController {
    public function init() {
        parent::init();
    }
    public function indexAction(){
        $menu = Rbac::getNav($this->userInfo['user_id']);
        $this->displayTemplate('index/index' , array(
            'menu' => $menu ,
            'userInfo'=>$this->userInfo,
        ) );
    }
    public function homeAction(){
        $Business = Common::ImportBusiness("Order" ,"Common");
        $today_order_num = $Business->getTodayOrderNum();
        $order_num = $Business->getAllOrderNum();
        $today_order_money = $Business->getTodayOrderMoney();
        $all_order_money = $Business->getAllOrderMoney();

        //昨日订单数据
        $yestady_order_num = $Business->getYestadyOrderNum();
        //昨日订单金额
        $yestady_order_money = $Business->getYestadyOrderMoney();
        $this->displayTemplate('index/main' , array(
            'userInfo'=>$this->userInfo,
            'today_order_num'=>$today_order_num,
            'order_num'=>$order_num,
            'today_order_money'=>$today_order_money,
            'all_order_money'=>$all_order_money ,
            'yestady_order_num' => $yestady_order_num,
            'yestady_order_money' => $yestady_order_money,
        ) );
    }

    public function getNewOrderAction(){
        $OrdersModel = new OrdersModel();
        $list = $OrdersModel->getDataBySql("select * from orders where status = 1 ");
        $info = isset($list[0]) ? $list[0] : [] ;
        if( !empty($info )){
            Common::EchoResult(1 , "ok" ,['has_new' => 1 ] );
        }
        if( !empty($info )){
            Common::EchoResult(1 , "ok" ,['has_new' =>  0  ] );
        }
    }

    public function getNewwithdrawAction(){
        $MerchantWithDrawModel = new MerchantWithDrawModel();
        $list = $MerchantWithDrawModel->getDataBySql("select * from merchants_withdraw where status = 1 ");
        $info = isset($list[0]) ? $list[0] : [] ;
        if( !empty($info )){
            Common::EchoResult(1 , "ok" ,['has_new' => 1 ] );
        }
        if( !empty($info )){
            Common::EchoResult(1 , "ok" ,['has_new' =>  0  ] );
        }
    }
}
?>