<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 * @des 商户的业务逻辑文件
 */
class MerchMoneyLogBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.* , b.name ,b.mid ,b.account  from merchants_money_log as a left join  merchants as b on a.mid = b.mid 
{$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from merchants_money_log as a left join  merchants as b on a.mid = b.mid   
       {$getWhere} " ;
        $data =  $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
        $list = $data['list'];
        $total = $data['total'];
        return array('list' => $list , 'total' => $total );
    }
    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['mid']) && $params['mid']){
            $getWhere.=" and b.`mid` = '{$params['mid']}' ";
        }
        if(isset($params['account']) && $params['account']){
            $getWhere.=" and b.`account` = '{$params['account']}' ";
        }
        if(isset($params['type']) AND  $params['type'] ){
            $getWhere.=" and a.`type` = '{$params['type']}' ";
        }
        return $getWhere;
    }
}