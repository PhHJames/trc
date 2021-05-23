<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_StatisController extends Admin_BaseAuthController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        $params = $this->getParams();
        $begin_date = isset($params['begin_date']) ? $params['begin_date'] :  '';
        $end_date = isset($params['end_date']) ? $params['end_date'] :  '';

        $begin_date = $begin_date ? $begin_date :  date("Y-m-d" , strtotime("-7 days"));
        $end_date = $end_date ? $end_date :  date("Y-m-d");
        $days = Tools::diffBetweenTwoDays($begin_date , $end_date );
        if($days > 30 or $days <= 0  ){
            $this->showError("温馨提示" , "时间范围必须大于1小于30");
        }
        $commonOrderBusiness = Common::ImportBusiness("Order" , "Common");
        $list = $commonOrderBusiness->getStatisByDate( $begin_date , $end_date);
        $list = Tools::arraySort($list , "day" , "desc");
        $hash_sum = [];
        $total_order_money_sum = 0 ;
        $success_order_money_sum = 0 ;
        $error_order_money_sum = 0 ;
        $success_merch_money_sum = 0 ;
        $total_order_num_sum = 0 ;
        $success_order_num_sum = 0 ;
        $error_order_num_sum = 0 ;
        foreach ($list as $key => $val ){
            $total_order_money_sum+= $val['total_order_money'];
            $success_order_money_sum+= $val['success_order_money'];
            $error_order_money_sum+= $val['error_order_money'];
            $success_merch_money_sum+= $val['success_merch_money'];
            $total_order_num_sum+= $val['total_order_num'];
            $success_order_num_sum+= $val['success_order_num'];
            $error_order_num_sum+= $val['error_order_num'];
        }
        //print_r($list);
        $res = array(
            'begin_date' => $begin_date,
            'end_date' => $end_date,
            'list' => $list,
            'total_order_money_sum'=>$total_order_money_sum,
            'success_order_money_sum'=>$success_order_money_sum,
            'error_order_money_sum'=>$error_order_money_sum,
            'success_merch_money_sum'=>$success_merch_money_sum,
            'total_order_num_sum'=>$total_order_num_sum,
            'success_order_num_sum'=>$success_order_num_sum,
            'error_order_num_sum'=>$error_order_num_sum,
        );
        $this->displayTemplate('statis/index' , $res);
    }



}