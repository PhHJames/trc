<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2016-06-27 16:54:41
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2016-07-07 09:48:54
 * @系统变量
 */
class Source_Sysconfig{
	static $_instance; //存储对象
	//redis的key
	public   $redis_key = "system_sysconfig" ;
	//过期时间
	public  $sysconfig_expire = 3600 ;
	//选择的数据库
	public  $redis_db = 0 ;
	public function __construct(){

	}
	public static function getInstance()
    {
        if (!isset(self::$_instance) or FALSE == (self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	//从redis获取 系统变量
	public   function getSysconfigFromRedisCache(){
		$redisObject = RedisDB::getInstance($this->redis_db)->handler;
		$sysconfig = $redisObject->HGETALL($this->redis_key);
		if($sysconfig && !empty($sysconfig)){
			return $sysconfig ;
		}
		$sysconfigModel = new SysconfigModel();
		$data = $sysconfigModel->getSysconfig();
		$hval = array();
		if($data){
			foreach($data as $key => $val ){
				$hval[$val['varname']] = $val['value'];
			}
		}
		if($hval){
			$redisObject->HMset($this->redis_key, $hval);
		}
		return $hval;
	}
	//获取某个配置变量的值
	public  function getVal( $key ){
		$data = $this->getSysconfigFromRedisCache();
		return isset($data[$key]) ? trim($data[$key]) : '' ;
	}
    //获取固定金额的配置
    public function getMixMoney(){
        $mix_money = $this->getVal("xianyu_mix_money");
        if( empty($mix_money )){
            return [];
        }
        $mix_money = explode("\n" , $mix_money);
        $hash = [] ;
        foreach ($mix_money as $item) {
            $item = $item * 100 ;
            $hash[] = $item ;
        }
        return $hash ;
    }

    //获取哪些商户的id允许下单测试
    public function getAllowPlace(){
        $mix_money = $this->getVal("merch_allow_place");
        if( empty($mix_money )){
            return [];
        }
        $mix_money = explode("\n" , $mix_money);
        $hash = [] ;
        foreach ($mix_money as $item) {
            $hash[] = $item ;
        }
        return $hash ;
    }
}