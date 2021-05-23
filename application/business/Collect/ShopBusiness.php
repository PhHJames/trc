<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 */
class ShopBusiness extends AbstractModel {
    public function getCollectShopList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getCollectShopWhere($params);
        $sql = "select a.*,b.account,b.account as c_account  , b.name as c_name   from collect_shop as a 
        left join collect_user as b on a.user_id = b.user_id   {$getWhere} order by a.create_date desc "   ;
        $sql_count = "select count(*) as c   from collect_shop as a 
        left join collect_user as b on a.user_id = b.user_id {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getCollectShopWhere($params=array()){
        $getWhere = "";
        if(isset($params['user_id']) && $params['user_id']){
            $params['user_id'] = addslashes($params['user_id']);
            $getWhere.=" and a.`user_id` = '{$params['user_id']}' ";
        }
        if(isset($params['account']) && $params['account']){
            $params['account'] = addslashes($params['account']);
            $getWhere.=" and b.`account` = '{$params['account']}' ";
        }
        if(isset($params['shop_id']) AND $params['shop_id']){
            $params['shop_id'] = intval($params['shop_id']);
            $getWhere.=" and a.`shop_id` = '{$params['shop_id']}' ";
        }
        if(isset($params['shop_name']) AND $params['shop_name']){
            $params['shop_name'] = addslashes($params['shop_name']);
            $getWhere.=" and a.`shop_name` = '{$params['shop_name']}' ";
        }
        return $getWhere;
    }

    public function auto_match( $params ){
        $user_id = isset($params['user_id'])?trim($params['user_id']): 0 ;
        $auto_match = isset($params['auto_match'])?intval($params['auto_match']): 0 ;
        $shop_id = isset($params['shop_id'])?intval($params['shop_id']): 0 ;
        if( empty($user_id) ){
            throw new Exception("参数缺失");
        }
        if( empty($shop_id) ){
            throw new Exception("缺少店铺ID");
        }
        $model = new CollectShopModel();
        $info = $model->getInfo( array('user_id' => $user_id , 'shop_id' => $shop_id  ) );
        if( empty($info) ){
            throw new Exception( "没有查询到店铺！！");
        }
        $request = array(
            'auto_match' => $auto_match ,
        );
        $status   = $model->updateData($request , ['user_id' => $user_id, 'shop_id' => $shop_id] );
        if( !$status ){
            throw new Exception( "修改失败请稍后");
        }
        return true ;
    }

}