<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:13:56
 * @des 代理商的业务逻辑文件
 */
class AgentMoneyLogBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.* , b.name ,b.agent_id ,b.account  from agent_money_log as a left join  agent as b on a.agent_id = b.agent_id 
{$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from agent_money_log as a left join  agent as b on a.agent_id = b.agent_id   
       {$getWhere} " ;
        $data =  $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
        $list = $data['list'];
        $total = $data['total'];
        return array('list' => $list , 'total' => $total );
    }
    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['agent_id']) && $params['agent_id']){
            $getWhere.=" and b.`agent_id` = '{$params['agent_id']}' ";
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