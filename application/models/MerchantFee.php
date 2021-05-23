<?php
/**
 * Created by 不要复制我的代码.
 * User: 不要复制我的代码
 * Date: 2019/5/14 0014
 * Time: 上午 11:45
 */
class MerchantFeeModel extends AbstractModel{
    protected $table = "merchants_channel_fee" ;
    protected $dbIndex = 0  ;
    //获取商户已经开通的费率
    public function getMerchFee($mid){
        $sql = "select a.* , b.alias,b.min_money,b.max_money from {$this->table} as a left join channel as b on a.channel = b.channel where a.mid = '{$mid}'
 and a.status = '1' and b.status = 1   ";
        return $this->db($this->dbIndex)->find( $sql);
    }
}