<?php
/**
 * Created by 不要复制我的代码 .
 * User: Administrator
 * Date: 2019/10/20 0020
 * Time: 18:23
 */
class CollectShopModel extends AbstractModel{
    protected $table = "collect_shop" ;
    protected $dbIndex = 0  ;
    public function execSql($sql){
        return $this->db($this->dbIndex)->Exec( $sql);
    }
}