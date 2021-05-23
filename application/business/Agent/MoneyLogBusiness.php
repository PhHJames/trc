<?php
/**
 * Created by Awe.
 * User: Administrator
 * Date: 2019/10/20 0020
 * Time: 18:29
 */
class MoneyLogBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.*  from agent_money_log as a   {$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from agent_money_log as a  {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['type']) && $params['type']){
            $getWhere.=" and a.`type` = '{$params['type']}' ";
        }
        if(isset($params['agent_id']) && $params['agent_id']){
            $getWhere.=" and a.`agent_id` = '{$params['agent_id']}' ";
        }
        return $getWhere;
    }

}