<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-04-26 22:59:32
 * @des 商户 业务文件
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
        $getWhere = "";
        if(isset($params['merch_order_sn']) && $params['merch_order_sn']){
            $getWhere.=" and a.`merch_order_sn` = '{$params['merch_order_sn']}' ";
        }
        if(isset($params['mid']) && $params['mid']){
            $getWhere.=" and a.`mid` = '{$params['mid']}' ";
        }

        if(isset($params['status']) AND $params['status']   ){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        return $getWhere;
    }

}