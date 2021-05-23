<?php  
class Agent_IndexController extends Agent_CenterController {
    public function init() {
        parent::init();
    }
    public function indexAction(){
        $menu =  $this->getMenu();
        $this->displayTemplate('index/index' , array(
            'menu' => $menu ,
            'userInfo'=>$this->userInfo,
        ) );
    }
    public function homeAction(){
        global $_G ;
        $AgentModel = new AgentModel();
        $account = $AgentModel->getInfo( array('agent_id' => $this->userInfo['agent_id']) );
        $Business = Common::ImportBusiness("Order" ,"Common");
        $agent_id = $this->agent_id ;
        $today_order_num = $Business->getTodayOrderNum( " AND agent_id = '{$agent_id}' " );
        $order_num = $Business->getAllOrderNum(" AND agent_id = '{$agent_id}' ");
        $today_order_money = $Business->getTodayOrderMoney(" AND agent_id = '{$agent_id}' ");
        $all_order_money = $Business->getAllOrderMoney(" AND agent_id = '{$agent_id}' ");
        $todaySuccessRate = $Business->todaySuccessRate("AND agent_id = '{$agent_id}'");
        $this->displayTemplate('index/main' , array(
            'userInfo'=>$this->userInfo,
            'account' => $account ,
            'today_order_num'=>$today_order_num,
            'order_num'=>$order_num,
            'today_order_money'=>$today_order_money,
            'all_order_money'=>$all_order_money,
            'todaySuccessRate'=>$todaySuccessRate
        ) );
    }
}
?>