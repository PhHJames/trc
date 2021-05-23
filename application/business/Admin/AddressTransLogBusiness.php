<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 */
class AddressTransLogBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.*   from address_trans_log as a 
{$getWhere} order by a.id desc "   ;
        $sql_count = "select count(*) as c   from address_trans_log as a 
       {$getWhere} " ;
        $data =  $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
        $list = $data['list'];
        $total = $data['total'];
        return array('list' => $list , 'total' => $total );
    }
    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['from']) && $params['from']){
            $getWhere.=" and a.`from` = '{$params['from']}' ";
        }
        if(isset($params['to']) && $params['to']){
            $getWhere.=" and a.`to` = '{$params['to']}' ";
        }
        if(isset($params['address']) && $params['address']){
            $getWhere.=" and  (a.`to` = '{$params['address']}' or a.`from` = '{$params['address']}' )  ";
        }
        if(isset($params['txid']) && $params['txid']){
            $getWhere.=" and a.`txid` = '{$params['txid']}' ";
        }
        return $getWhere;
    }
}