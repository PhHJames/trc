<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 * @des 商户的业务逻辑文件
 */
class MerchBankBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.*  , b.name , b.account  from merchants_bank as a left join  merchants as b on a.mid = b.mid 
{$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from merchants_bank as a left join  merchants as b on a.mid = b.mid {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['mid']) && $params['mid']){
            $getWhere.=" and b.`mid` = '{$params['mid']}' ";
        }
        if(isset($params['account']) && $params['account']){
            $getWhere.=" and b.`account` = '{$params['account']}' ";
        }
        if(isset($params['status']) AND  $params['status'] ){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        return $getWhere;
    }

   public function doManager( $params ){
       $id = isset($params['id'])?intval($params['id']):'';
       $remark = isset($params['remark'])?trim($params['remark']):'';
       $status = isset($params['status'])?trim($params['status']):'';
       $MerchantsBankModel = new MerchantsBankModel();
       $info  = $MerchantsBankModel->getInfo( array('id' => $id ) );
       if( empty($info )){
           throw new Exception("暂无数据");
       }
       $update = array(
           'status' => $status ,
           'remark' => $remark
       );
       $MerchantsBankModel->updateData($update , array('id' => $id ) ) ;
       return true ;
   }

}