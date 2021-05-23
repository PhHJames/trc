<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-03-26 14:57:33
 */
class Collect_OrderController extends Collect_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxOrderList();
            exit;
        }
        $Business = Common::ImportBusiness("Order" ,"Common" );
        $where = " AND user_id = '{$this->userInfo['user_id']}'";
        $todayData = $Business->getTodayOrderNum($where);//今日订单数
        $TodayOrderMoney = $Business->getTodayOrderMoney($where);//今日订单金额
        $yestdayData = $Business->getYestadyOrderNum($where);//昨日订单数
        $yestdayOrderMoney = $Business->getYestadyOrderMoney($where);//昨日订单金额
        $res = array(
            'pageSize'=> 10 ,
            'statusData' => Enum::$orderStatus,
            'todayData' => $todayData,
            'TodayOrderMoney' => $TodayOrderMoney,
            'yestdayData' => $yestdayData,
            'yestdayOrderMoney' => $yestdayOrderMoney
        );
        $this->displayTemplate('order/order_index' , $res);
    }
    private function getAjaxOrderList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("Order" ,"Collect" );
        $params['user_id'] = $this->userInfo['user_id'];
        $data = $Business->getOrderList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $orderStatus = Enum::$orderStatus ;
            $rsyncOrderStatus = Enum::$rsyncOrderStatus ;
            $OrderSupplementStatus = Enum::$OrderSupplementStatus ;
            foreach ($list as $key => $value) {
                $list[$key]['r_money'] ="￥". $value['r_money']/100 ;
                $statusData = Enum::getVal($value['status'] ,$orderStatus )  ;
                $list[$key]['status_string'] =  isset($statusData['value']) ? $statusData['value'] : ""  ;
                $rsyncStatusData = Enum::getVal($value['rsync_status'] ,$rsyncOrderStatus )  ;
                $list[$key]['rsync_status'] =  isset($rsyncStatusData['value']) ? $rsyncStatusData['value'] : ""  ;
                $supplementData = Enum::getVal($value['is_supplement'] ,$OrderSupplementStatus )  ;
                $list[$key]['is_supplement'] =  isset($supplementData['value']) ? $supplementData['value'] : ""  ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
   
}