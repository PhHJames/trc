<?php
/**
 * Created by 不要复制我的代码 .
 * User: Administrator
 * Date: 2019/10/20 0020
 * Time: 18:23
 */
class MerchantsBankModel extends AbstractModel{
    protected $table = "merchants_bank" ;
    protected $dbIndex = 0  ;
    //查询用户已经存在的银行卡
    public function getHasExistsCard( $mid ){
        $sql = "select * from " . $this->table . " where status in (1 , 2 ) and mid = '{$mid}'  " ;
        return $this->db($this->dbIndex)->find( $sql );
    }

    public function isAllowAddCard($mid){
        $res = $this->getHasExistsCard($mid);
        $count_config = intval(Source_Sysconfig::getInstance()->getVal("max_card_number"));
        if( count($res) >= $count_config){
            return false;
        }
        return true ;
    }
    //获取商户审核通过的银行卡
    public function getAvalialeCard($mid){
        $sql = "select * from " . $this->table . " where status in (2 ) and mid = '{$mid}'  " ;
        return $this->db($this->dbIndex)->find( $sql );
    }
}