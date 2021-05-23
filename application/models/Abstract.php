<?php

class AbstractModel
{
  	protected $db;
  	protected $_table = null ;
    protected $_primary  = null ;
  	public function __construct()
    {
      //$this->db = DB_mode::getInstance($env = null);
    }
    protected function db($dbindex = 0 ){
      return  DB_mode::getInstance($dbindex);
    }
    public function getdbprefix($dbindex = 0){
        global $_G;
        $db = ($dbindex == 0 ) ? "DB" : "DB".$dbindex ;
        return $_G['config'][$db]['database']['params']['dbprefix'] ;
    }
    public function getInfo( $where  = array() , $lock = false  ){
        //echo $this->table  ;
        //$sql = "select  *   from " . $this->table .  " where ime = :ime limit 1   " ;
        $wheres = '' ;
        foreach ($where as $key => $val ){
            $wheres .= "{$key} = :{$key} AND " ;
        }
        $wheres = substr($wheres, 0, strlen($wheres) - 4);
        $sql = "select * from `" . $this->table ."` where {$wheres} limit 1 " ;
        if($lock){
            $sql .= " for update";
        }
        return $this->db($this->dbIndex)->findOne($sql , $where );
        //echo $wheres ;
    }
    public function updateData($update  , $where){
        return $this->db($this->dbIndex)->Update($this->table , $update , $where );
    }
    public function insertData($data){
        return $this->db($this->dbIndex)->Insert($this->table ,$data );
    }

    public function getDataBySql( $sql , $dbindex = 0  ){
        $object = $this->db($dbindex);
        return $object->find( $sql );
    }
    /*
    #data_sql 查询的数据sql
    #count_sql 查询数据总数的sql
    #pageSize 每一页显示的数据条数
    #p 第几页
    #dbindex 第几个数据库
    */
    public function getCommonDataList($data_sql , $count_sql , $pageSize , $p = 1 , $dbindex = 0 ){
        $object = $this->db($dbindex);
        $limit = ($p-1)*$pageSize;
        $limit.=",{$pageSize}";
        $count_data = $object->find($count_sql);
        $total = isset($count_data[0]['c']) ? $count_data[0]['c'] : 0;
        $pageObject = new AdminPage($total , $pageSize);
        $pageString =  $pageObject->show();
        $data_sql.=" LIMIT {$limit}" ;
        $list = $object->find($data_sql);
        return array('list'=>$list , 'pageString'=>$pageString , 'total' => $total );
    }

}



?>
