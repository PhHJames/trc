<?php
class DB_mode  {
  private $dbh ;
  static $_instance; //存储对象
  public $dbprefix = "" ; //这个地方定义主要model层 也可以使用这个表的前缀，business层可以不通过这个获取表的前缀
  private function  __construct($dbindex = 0 )
  {
    global $_G ;
    $db = ($dbindex == 0 ) ? "DB" : "DB".$dbindex ;
    if(!isset($_G['config'][$db]) OR !$_G['config'][$db]){
      exit("DB INDEX IS ERROR ");
    }
    $data = $_G['config'][$db]['database']['params'] ;
    $dsn = 'mysql:dbname='.$data['database'].';host='.$data['hostname'].";charset=".$data['charset'];
    $user = $data['username'];
    $password = $data['password'];
    $this->dbprefix = $data['dbprefix'];
    try {
        $this->dbh = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }   

  }
  public static function getInstance($dbindex = 0){
    if(!isset(self::$_instance[$dbindex]) or  FALSE == (self::$_instance[$dbindex] instanceof self)){
      self::$_instance[$dbindex] = new self($dbindex);
    }
    return self::$_instance[$dbindex];
  }
    
    
    //执行sql
    public  function Exec($sql,$data=null)
    {       
      $sth = $this->dbh->prepare($sql);
      if(empty($data))
      {
        $sth->execute();
      }else
      {
        $sth->execute($data);
      } 
       $ct = $sth->rowCount();  
       return $ct;
      
    }
    //返回插入的那个ID
    


    //查询一行 返回数组    
    public function findOne($sql , $data=null)
    {
      $sth = $this->dbh->prepare($sql);       
    if(empty($data))
      {
        $sth->execute();
      }else
      {
        $sth->execute($data);
      } 
    $result = $sth->fetch(PDO::FETCH_ASSOC);
    return $result ;

    }

    //查询多行返回数组
    public function find($sql , $data =null)
    {  
            $sth = $this->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));     
            if(empty($data))
            {
                $sth->execute();
            }else
            {
                $sth->execute($data);
            }
                    
      $result = $sth->fetchAll(PDO::FETCH_ASSOC);   
      return $result;

    }

   
    
    public function Insert($table ,$data){
        if(empty($data) or  empty($table)){
            return false;
        }
        $sql = "INSERT INTO ".$table . " SET ";
        foreach($data as $key=>$value){            
            $sql =$sql." `".$key."`=:".$key.",";
        }
        $sql = substr($sql ,0 ,strlen($sql)-1);
        $sth = $this->dbh->prepare($sql);
        $sth->execute($data);
        $err =$this->dbh->errorInfo();
        if( is_array($err) and  $err[0] == "00000")
           {
             return $this->dbh->lastInsertId($table);
           }else
           {
               return false;
           }               
    }
        //批量添加(二维数组)
    public function addAll($table, $data)
    {
        if (empty($data) or empty($table)) {
            return false;
        }
        $sqlPre = "INSERT INTO `" . $table . "`(";
        $tailSql = "";
        $sql = "INSERT INTO `" . $table . "`(";
        foreach ($data as $key => $val) {
            $tailSql .= "(";
            foreach ($val as $kk => $vv) {
                if ($key == 0) {
                    $field[] = $kk;
                }
                $tailSql .= "'{$vv}',";
            }
            $tailSql = rtrim($tailSql, ",");
            $tailSql .= "),";
        }
        $tailSql = rtrim($tailSql, ",");
        foreach ($field as $fkey => $fval) {
            $sqlPre .= "`{$fval}`,";
        }
        $sqlPre = rtrim($sqlPre, ",");
        $sql = $sqlPre . ") VALUES " . $tailSql;

        return $this->Exec($sql);
    }
    public function Update($table , $data ,$where){
         if(empty($data) or  empty($table) or empty($where)){
            return false;
        }
        $sql = "Update ".$table . " SET ";
        foreach($data as $key=>$value){            
            $sql =$sql." `".$key."`=:".$key.",";
        }
        $sql = substr($sql ,0 ,strlen($sql)-1) ; 
        $sql =$sql. " where ";
        foreach($where as $key=>$value){            
            $sql =$sql." `".$key."`=:".$key." and";
        }
        $sql = substr($sql ,0 ,strlen($sql)-3) ;
        $sth = $this->dbh->prepare($sql);
        $full = array_merge($data,$where);
        $sth->execute($full);
      $effid = $sth->rowCount();  
    
       if( $effid>0 )
       {
           return  $effid;
       }else
       {
           $err =$this->dbh->errorInfo();
           if( is_array($err) and  $err[0] == "00000")
           {
               return true;
           }else
           {
               return false;
           }
       }           
    }
    //事物开始
    public function beginTransaction(){
      //$this->dbh->setAttribute(PDO::ATTR_ERRMODE, 2);
      $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->dbh->beginTransaction();
    }
    //事物回滚
    public function rollBack(){
      $this->dbh->rollBack();
    }
    //事物提交
    public function commit(){
      $this->dbh->commit();
    }
}
