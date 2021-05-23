<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 */
class OrdersPaymentLogBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.*   from orders_payment_log as a 
{$getWhere} order by a.create_date desc "   ;
        $sql_count = "select count(*) as c   from orders_payment_log as a 
       {$getWhere} " ;
        $data =  $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
        $list = $data['list'];
        $total = $data['total'];
        return array('list' => $list , 'total' => $total );
    }
    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['to_address']) && $params['to_address']){
            $getWhere.=" and a.`to_address` = '{$params['to_address']}' ";
        }
        if(isset($params['from_address']) && $params['from_address']){
            $getWhere.=" and a.`from_address` = '{$params['from_address']}' ";
        }
        if(isset($params['order_sn']) && $params['order_sn']){
            $getWhere.=" and a.`order_sn` = '{$params['order_sn']}' ";
        }
        if(isset($params['transaction_id']) && $params['transaction_id']){
            $getWhere.=" and a.`transaction_id` = '{$params['transaction_id']}' ";
        }
        return $getWhere;
    }
}