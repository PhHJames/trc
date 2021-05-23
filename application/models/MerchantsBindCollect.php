<?php
/**
 * Created by 不要复制我的代码 .
 * User: Administrator
 * Date: 2019/10/20 0020
 * Time: 18:23
 */
class MerchantsBindCollectModel extends AbstractModel{
    protected $table = "merchants_bind_collect" ;
    protected $dbIndex = 0  ;
    //查询某个商户绑定的码商 返回用户id
    public function getMerchCollect( $mid ){
        $sql = "select * from " . $this->table . " where  mid = '{$mid}'  " ;
        $list =  $this->db($this->dbIndex)->find( $sql );
        if( empty($list )){
            return [];
        }
        $hash = [];
        foreach ($list as $kk => $vv ){
            $hash[] = $vv['user_id'];
        }
        return $hash;
    }

    //查询用户绑定的商户
    public function getUserBindMerch( $user_id ){
        $sql = "select * from " . $this->table . " where  user_id = '{$user_id}'  " ;
        return $this->db($this->dbIndex)->findOne( $sql);
    }
}