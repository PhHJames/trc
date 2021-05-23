<?php


  class RedisDB
  {
    static $_instance; //存储对象
    public $handler ;
      private function __construct($dbindex = 0)
      {  
        global $_G ;
        $data = $_G['config']['redis']['redis']['params']; 
        if ( !extension_loaded('redis') ) {
            throw new Exception("REDIS NOT  SUPPORT", 1);
        }      
        $this->handler =  new Redis();  
        //从配置读取
        $this->handler->connect($data['hostname'],$data['port']);
        $this->handler->auth($data['auth']);
		    $this->handler->select($dbindex); 
      }
      public static function getInstance($dbindex = 0){
        if(!isset(self::$_instance[$dbindex]) or  FALSE == (self::$_instance[$dbindex] instanceof self)){
          self::$_instance[$dbindex] = new self($dbindex);
        }
        return self::$_instance[$dbindex];
      }


    /**key value  get**/
    public  function GET($key)
    {
      return  $this->handler->get($key);
    }
    /**key value  set  过期时间为 $exp**/
    public  function SET($key ,$value ,$exp)
    {
        $this->handler->setex($key ,$exp ,$value );
    }

    /*移除数据$key*/
    public  function REMOVE($key)
    {
        $this->handler->del($key);

    }

      /*设置数据的过期时间$key*/
    public  function EXPIRE($key ,$exp)
    {
        $this->handler->expire($key ,$exp);
    }

    /**Hash 相关**/

    public  function HGET($domain , $key)
    {
        return $this->handler->hGet($domain , $key);
    }
    public  function HSET ($domain ,$key ,$value )
    {
          $this->handler->hSet($domain , $key);
    }

    public  function HREMOVE($domain ,$key)
    {
        $this->handler->hDel($domain , $key);

    }

     /*插入列表*/
    public  function  PushList($channel,$data)
    {
          $this->handler->lPush($channel,$data);
    }

    /*从列表中获取*/
    public function  POPList($channel)
    {
        return  $this->handler->lPop($channel);
    }


  }














 ?>
