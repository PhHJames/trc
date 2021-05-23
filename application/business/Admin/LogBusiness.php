<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 */
class LogBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.*   from admin_log as a 
{$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from admin_log as a 
       {$getWhere} " ;
        $data =  $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
        $list = $data['list'];
        $total = $data['total'];
        return array('list' => $list , 'total' => $total );
    }
    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['member_id']) && $params['member_id']){
            $getWhere.=" and a.`member_id` = '{$params['member_id']}' ";
        }
        if(isset($params['username']) && $params['username']){
            $getWhere.=" and a.`username` = '{$params['username']}' ";
        }
        if(isset($params['url']) AND  $params['url'] ){
            $getWhere.=" and a.`url` = '{$params['url']}' ";
        }
        return $getWhere;
    }
}