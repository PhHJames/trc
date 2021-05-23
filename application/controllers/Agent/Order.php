<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last Modified by:   Awe
 * @Last Modified time: 2019-03-26 14:57:33
 */
class Agent_OrderController extends Agent_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxOrderList();
            exit;
        }
        $Business = Common::ImportBusiness("Order" ,"Agent" );
        $res = array(
            'pageSize'=> 10 ,
            'statusData' => Enum::$orderStatus
        );
        $this->displayTemplate('order/order_index' , $res);
    }
    private function getAjaxOrderList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("Order" ,"Agent" );
        $params['agent_id'] = $this->userInfo['agent_id'];
        $data = $Business->getOrderList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $orderStatus = Enum::$orderStatus ;
            $rsyncOrderStatus = Enum::$rsyncOrderStatus ;
            $OrderSupplementStatus = Enum::$OrderSupplementStatus ;
            foreach ($list as $key => $value) {
                //$list[$key]['money'] ="￥". $value['money']/100 ;
                //$list[$key]['real_money'] ="￥". $value['real_money']/100 ;
                $list[$key]['agent_real_money'] ="<font color='green'><b>". $value['agent_real_money'] ."</font></b>";
                $statusData = Enum::getVal($value['status'] ,$orderStatus )  ;
                $list[$key]['status'] =  isset($statusData['value']) ? $statusData['value'] : ""  ;
                $rsyncStatusData = Enum::getVal($value['rsync_status'] ,$rsyncOrderStatus )  ;
                $list[$key]['rsync_status'] =  isset($rsyncStatusData['value']) ? $rsyncStatusData['value'] : ""  ;
                $supplementData = Enum::getVal($value['is_supplement'] ,$OrderSupplementStatus )  ;
                $list[$key]['is_supplement'] =  isset($supplementData['value']) ? $supplementData['value'] : "无"  ;
                $list[$key]['complete_date'] =  $value['complete_date'] ? $value['complete_date'] : "无"  ;
                //$list[$key]['a_fee'] =  $value['a_fee'] * 100  . "%"   ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
   
}