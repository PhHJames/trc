<?php
/**
 * Created by 不要复制我的代码.
 * User: 不要复制我的代码
 * Date: 2019/5/14 0014
 * Time: 上午 11:45
 */
class BankTypeModel extends AbstractModel{
    protected $table = "bank_type" ;
    protected $dbIndex = 0  ;
    public function getCard( $status = null ){
        $where = "";
        if( $status !== null ){
            $where .= " AND status = '{$status}' "  ;
        }
        $sql = "select * from " . $this->table . " where 1 = 1  {$where}";
        return $this->db($this->dbIndex)->find($sql);
    }
}