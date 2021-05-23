<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last Modified by:   Awe
 * @Last Modified time: 2019-04-26 22:59:32
 * @des 代理商订单业务文件
 */
class OrderBusiness extends AbstractModel {
    public function getOrderList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getOrderWhere($params);
        $sql = "select a.*  from orders as a   {$getWhere} order by a.create_date desc "   ;
        $sql_count = "select count(*) as c   from orders as a  {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getOrderWhere($params=array()){
        $getWhere = " AND agent_id =  '{$params['agent_id']}' ";
        if(isset($params['merch_order_sn']) && $params['merch_order_sn']){
            $getWhere.=" and a.`merch_order_sn` = '{$params['merch_order_sn']}' ";
        }
        if(isset($params['status']) AND $params['status']   ){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        return $getWhere;
    }
    public function orderTotal($customer_id){
        $today = date("Y-m-d" ,time());
        $yestady = date("Y-m-d" , strtotime("-1 days "));
        $sql = "select sum(real_money) as money from  orders where mid = '{$customer_id}' and date_format(`create_date` , '%Y-%m-%d' ) = '{$today}' and status = '2'   limit 1  ";
        $info = $this->db(0)->findOne($sql);
        $t_count = isset($info['money']) ? $info['money'] : 0 ;

        $sql = "select sum(real_money) as money from  orders where mid = '{$customer_id}' and date_format(`create_date` , '%Y-%m-%d' ) = '{$yestady}' and status = '2'   limit 1  ";
        //echo $sql;
        $info = $this->db(0)->findOne($sql);
        $y_count = isset($info['money']) ? $info['money'] : 0 ;
        return array(
            't_count' => $t_count  / 100 ,
            'y_count'=>$y_count / 100
        );
    }
}