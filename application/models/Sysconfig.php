<?php
/**
 * @Author: Awe
 * @Date:   2016-06-24 11:08:27
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2016-06-24 11:19:24
 */
class SysconfigModel extends AbstractModel{
    public function getSysconfig(){
        $object  = $this->db(0) ;
        $sql = "select  varname , value  from sysconfig as a order by disorder desc  " ;
        return $object->find($sql);
    }
}