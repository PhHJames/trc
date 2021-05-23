<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last Modified by:   Awe
 * @Last Modified time: 2019-03-26 14:57:33
 */
class Agent_StatisticsController extends Agent_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        $begin_date = date("Y-m-d" , strtotime("-7 days"));
        $end_date = date("Y-m-d");
        $res = array(
            'begin_date' => $begin_date ,
            'end_date' => $end_date
        );
        $this->displayTemplate('statistics/index' , $res);
    }

    public function order_numberAction(){
        $params = $this->getParams();
        $begin_date = isset($params['begin_date']) ? $params['begin_date'] : "" ;
        $end_date = isset($params['end_date']) ? $params['end_date'] : ""  ;
        $diffDays = Tools::diffBetweenTwoDays($begin_date,$end_date) ;
        if($diffDays > 30 ){
            $this->echoListJson( 100  ,"日期不能超过30天" );
        }
        $Business = Common::ImportBusiness("Agent" ,"Common" );
        $list = $Business->order_number( $this->agent_id,  $begin_date , $end_date );
        //$this->echoListJson(1 ,"OK" , count($list)   ,$list );
        $this->echoListJson(0,"OK" , count($list)   ,$list );
    }

    public function order_moneyAction(){
        $params = $this->getParams();
        $begin_date = isset($params['begin_date']) ? $params['begin_date'] : "" ;
        $end_date = isset($params['end_date']) ? $params['end_date'] : ""  ;
        $diffDays = Tools::diffBetweenTwoDays($begin_date,$end_date) ;
        if($diffDays > 30 ){
            $this->echoListJson( 100  ,"日期不能超过30天" );
        }
        $Business = Common::ImportBusiness("Agent" ,"Common" );
        $list = $Business->order_money( $this->agent_id,  $begin_date , $end_date );
        $this->echoListJson(0,"OK" , count($list)   ,$list );
    }
}