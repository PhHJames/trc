<?php  
class Admin_CacheController extends Admin_BaseAuthController {
    public function init() {
        parent::init();
    }
    //如果需要新增其他缓存更新，请新增数组里面的元素
    public $cacheType = array(
        1 =>array(
            'key' => 'admin_user_permission_*' , 
            'name' => '后台用户权限缓存' ,
            'cache_type' => "redis" , 
            'redis_db' => 2 , 
        ),
        2 =>array(
            'key' => 'system_sysconfig' ,
            'name' => '配置缓存' ,
            'cache_type' => "redis" ,
            'redis_db' => 0 ,
        ),
    );

    public function indexAction(){
        $action = $this->getRequest()->getPost("action","");
        $action_array = array("show","doCache");
        $action = !in_array($action,$action_array) ? 'show' : $action ;
        if($action == 'show' ){
           $this->displayTemplate("cache/index" , array(
                'cache' => $this->cacheType,
            ) );
        }elseif($action == 'doCache'){
            $cache = isset($_POST['cache']) ? $_POST['cache'] : '';
            if(!$cache or empty($cache)){
                Common::EchoResult(0, "请选择缓存类型") ;
            }
            if(!array_key_exists($cache, $this->cacheType)){
                Common::EchoResult(0, "参数错误") ;
            }
            switch ($cache) {
                case '1':
                    $redisDB = $this->cacheType[$cache]['redis_db'];
                    $object = RedisDB::getInstance($redisDB)->handler ;
                    $keys = $this->cacheType[$cache]['key'];
                    if( stripos($keys , "*" ) !== false ){
                        $keys = $object->keys($keys);
                    }
                    $object->del($keys);
                    break;
                case '2':
                    $keys = $this->cacheType[$cache]['key'];
                    $redisDB = $this->cacheType[$cache]['redis_db'];
                    $object = RedisDB::getInstance($redisDB)->handler ;
                    $object->del($keys);
                    break;
                    break;
                default:
                    break;
            }
            Common::EchoResult(1, "清除成功") ;
        }
    }
}

