<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 */
class QrImportLogBusiness extends AbstractModel {
    public function getCollectQrImportLogList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getCollectQrImportLogWhere($params);
        $sql = "select a.*,b.account as c_account  , b.name as c_name   from qr_import_log as a 
        left join collect_user as b on a.user_id = b.user_id   {$getWhere} order by a.create_date desc "   ;
        $sql_count = "select count(*) as c   from qr_import_log as a 
        left join collect_user as b on a.user_id = b.user_id {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getCollectQrImportLogWhere($params=array()){
        $getWhere = "";
        if(isset($params['user_id']) && $params['user_id']){
            $params['user_id'] = addslashes($params['user_id']);
            $getWhere.=" and a.`user_id` = '{$params['user_id']}' ";
        }
        if(isset($params['status']) && $params['status']){
            $params['status'] = intval($params['status']);
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        if(isset($params['id']) && $params['id']){
            $params['id'] = addslashes($params['id']);
            $getWhere.=" and a.`id` = '{$params['id']}' ";
        }
        if(isset($params['shop_name']) AND $params['shop_name']){
            $params['shop_name'] = addslashes($params['shop_name']);
            $getWhere.=" and a.`shop_name` = '{$params['shop_name']}' ";
        }
        return $getWhere;
    }



}