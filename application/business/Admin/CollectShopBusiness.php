<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 */
class CollectShopBusiness extends AbstractModel {
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

    public function do_edit_shop($params){
        $shop_id = isset($params['shop_id']) ? intval($params['shop_id']) : 0 ;
        $day_limit_money = isset($params['day_limit_money']) ? trim($params['day_limit_money']) : 0 ;
        $remark = isset($params['remark']) ? trim($params['remark']) : "" ;
        $shop_name = isset($params['shop_name']) ? trim($params['shop_name']) : "" ;
        $CollectShopModel= new CollectShopModel();
        $info  = $CollectShopModel->getInfo(['shop_id' => $shop_id]);
        if( empty($shop_id )){
            throw new Exception("缺失店铺id");
        }
        if( empty($shop_name )){
            throw new Exception("请填写店铺名称");
        }
        if( empty($info )){
            throw new Exception("没有查询到店铺信息");
        }
        $day_limit_money = $day_limit_money * 100 ;
        if($day_limit_money > 0 ){
            if($day_limit_money < $info['day_limit_money']  ){
                throw new Exception("限额错误，当前数据库是：".($info['day_limit_money']/100) . ",不可以小于:" . ($info['day_limit_money']/100) );
            }
        }

        $update  = [
            'day_limit_money' => $day_limit_money ,
            'remark' => $remark ,
        ];
        $CollectShopModel->updateData($update , ['shop_id' => $shop_id] );
        return true ;
    }

}