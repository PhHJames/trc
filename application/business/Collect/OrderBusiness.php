<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-04-26 22:59:32
 * @des
 */
class OrderBusiness extends AbstractModel {
    public function getOrderList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getOrderWhere($params);
        $sql = "select a.* , b.qr_id from orders as a 
        left join collect_qrcode as b on a.qr_id = b.qr_id 
         {$getWhere} order by a.create_date desc "   ;
        $sql_count = "select count(*) as c from orders as a  left join collect_qrcode as b on a.qr_id = b.qr_id 
          {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getOrderWhere($params=array()){
        $getWhere = "";
        if(isset($params['order_sn']) && $params['order_sn']){
            $params['order_sn'] = addslashes($params['order_sn']);
            $getWhere.=" and a.`order_sn` = '{$params['order_sn']}' ";
        }
        if(isset($params['shop_name']) && $params['shop_name']){
            $params['shop_name'] = addslashes($params['shop_name']);
            $getWhere.=" and s.`shop_name` = '{$params['shop_name']}' ";
        }
        if(isset($params['user_id']) && $params['user_id']){
            $getWhere.=" and a.`user_id` = '{$params['user_id']}' ";
        }
        if(isset($params['qr_id']) && $params['qr_id']){
            $params['qr_id'] = intval($params['qr_id']);
            $getWhere.=" and a.`qr_id` = '{$params['qr_id']}' ";
        }
        if(isset($params['status']) AND $params['status']   ){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        return $getWhere;
    }
    public function orderTotal($user_id){
        $today = date("Y-m-d" ,time());
        $yestady = date("Y-m-d" , strtotime("-1 days "));
        $sql = "select sum(money) as money from  orders where user_id = '{$user_id}' and date_format(`create_date` , '%Y-%m-%d' ) = '{$today}' and status = '2'   limit 1  ";
        $info = $this->db(0)->findOne($sql);
        $t_count = isset($info['money']) ? $info['money'] : 0 ;

        $sql = "select sum(money) as money from  orders where user_id = '{$user_id}' and date_format(`create_date` , '%Y-%m-%d' ) = '{$yestady}' and status = '2'   limit 1  ";
        //echo $sql;
        $info = $this->db(0)->findOne($sql);
        $y_count = isset($info['money']) ? $info['money'] : 0 ;
        return array(
            't_count' => $t_count  / 100 ,
            'y_count'=>$y_count / 100
        );
    }
}