<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:13:56
 * @des  代理商的公共业务逻辑文件
 */
class AgentBusiness extends AbstractModel {
    //代理商的订单数统计
    public function order_number ( $agent_id  ,$begin_date , $end_date  ){
        $numDay = Tools::diffBetweenTwoDays($begin_date,$end_date);
        if($numDay <= 1 ){
            $numDay = 1 ;
        }
        $hash = [] ;
        for( $k = 0 ;$k<$numDay ; $k ++ ){
            $days = date("Y-m-d" , strtotime("-{$k}days"));
            //总订单数
            $sql = "select count(*) as c from orders where DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$days}' and agent_id = '{$agent_id}'   limit 1 ";
            $info = $this->db(0)->find($sql);
            $total = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
            //成功总订单数
            $sql = "select count(*) as c from orders where DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$days}' and status = 2   and agent_id = '{$agent_id}' limit 1 ";
            $info = $this->db(0)->find($sql);
            $success_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
            //待付款订单数
            $sql = "select count(*) as c from orders where DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$days}' and status = 1  and agent_id = '{$agent_id}'  limit 1 ";
            $info = $this->db(0)->find($sql);
            $wait_pay_num = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
            $hash[] = [
                'days' => $days ,
                'total'=>$total,
                'success_num'=>$success_num,
                'wait_pay_num'=>$wait_pay_num,
                'show_days' => ($days == date("Y-m-d") ) ? "今日" :  $days
            ];
        }
        return $hash;
    }

    //代理商的金额统计
    public function order_money ( $agent_id  ,$begin_date , $end_date ){
        $numDay = Tools::diffBetweenTwoDays($begin_date,$end_date);
        if($numDay <= 1 ){
            $numDay = 1 ;
        }
        $hash = [] ;
        for( $k = 0 ;$k<$numDay ; $k ++ ){
            $days = date("Y-m-d" , strtotime("-{$k}days"));

            //总订单金额
            $sql = "select sum(money) as c  from orders where  1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$days}' and agent_id = '{$agent_id}'  limit 1 ";
            $info = $this->db(0)->find($sql);
            $total_money = isset($info[0]['c']) ? $info[0]['c'] : 0 ;

            //成功总订单金额
            $sql = "select sum(agent_real_money) as c from orders where  1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$days}' and status = 2  and agent_id = '{$agent_id}' limit 1 ";
            $info = $this->db(0)->find($sql);
            $success_money= isset($info[0]['c']) ? $info[0]['c'] : 0 ;


            //待付款总订单金额
            $sql = "select sum(money) as c from orders where 1 = 1 and  DATE_FORMAT(create_date,'%Y-%m-%d' ) = '{$days}' and status = 1  and agent_id = '{$agent_id}' limit 1 ";
            $info = $this->db(0)->find($sql);
            $wait_pay_money = isset($info[0]['c']) ? $info[0]['c'] : 0 ;
            $hash[] = [
                'days' => $days ,
                'total_money'=>Common::foramtNumber($total_money , NUMBER_FORMAT),
                'success_money'=>Common::foramtNumber($success_money , NUMBER_FORMAT ),
                'wait_pay_money'=>Common::foramtNumber($wait_pay_money , NUMBER_FORMAT ),
                'show_days' => ($days == date("Y-m-d") ) ? "今日" :  $days
            ];
        }
        return $hash;
    }



}