<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 */
class PlatLogBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.*   from log as a 
{$getWhere} order by a.create_date desc "   ;
        $sql_count = "select count(*) as c   from log as a 
       {$getWhere} " ;
        $data =  $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
        $list = $data['list'];
        $total = $data['total'];
        return array('list' => $list , 'total' => $total );
    }
    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['type']) && $params['type']){
            $getWhere.=" and a.`type` = '{$params['type']}' ";
        }
        if(isset($params['level']) && $params['level']){
            $getWhere.=" and a.`level` = '{$params['level']}' ";
        }

        return $getWhere;
    }
}