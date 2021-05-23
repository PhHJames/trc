<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:13:56
 */
class AgentActionLogBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.*   from agent_action_log as a {$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from agent_action_log as a 
       {$getWhere} " ;
        $data =  $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
        $list = $data['list'];
        $total = $data['total'];
        return array('list' => $list , 'total' => $total );
    }
    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['username']) && $params['username']){
            $getWhere.=" and a.`username` = '{$params['username']}' ";
        }
        if(isset($params['url']) && $params['url']){
            $params['url'] = trim($params['url']);
            $getWhere.=" and a.`url` = '{$params['url']}' ";
        }
        if(isset($params['method']) && $params['method']){
            $params['method'] = trim($params['method']);
            $getWhere.=" and a.`method` = '{$params['method']}' ";
        }
        return $getWhere;
    }
}