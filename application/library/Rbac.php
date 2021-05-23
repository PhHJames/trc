<?php
/**
 * @Author: Awe
 * @Date:   2016-06-01 19:42:35
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-10-30 16:42:54
 * @权限类
 */
class Rbac{
    //当前的url地址
    public static  $visitUrl = '' ;
    //用的redis数据库
    public static $redis_db = 2 ; 
    //redis缓存时间
    public static $redis_expire = 3600;
    public static function GETPermission($url = null  , $uid){
        global $_G ;
        $redis_key = "admin_user_permission_{$uid}" ;
        $cache = RedisDB::getInstance(self::$redis_db)->get($redis_key);
        if($cache && !empty($cache ) ){
            //return unserialize($cache) ;
        }
        self::$visitUrl = strtolower($url) ; 
        $res = self::getUserModules($uid);
        $module_url = $res['allUrls'];
        $is_super = $res['is_super'];
        $result = array(
            'module_url' => $module_url,
            'is_super' => $is_super,
        );
        //print_r($module_url);
        $string = serialize($result) ;
        RedisDB::getInstance(self::$redis_db)->set($redis_key , $string , self::$redis_expire);
        return $result ; 
    }
    //获取用户所有的模块URl地址,和其他的信息
    public static function getUserModules( $user_id ){
        $Business = Common::ImportBusiness("AdminUser" );
        $data = $Business->getUserPrivilegsModules($user_id);
        $module_url =  isset($data['module_url']) ? $data['module_url'] : array();
        $AdminUserModel = new AdminUserModel();
        $special = $AdminUserModel->getUserSpecial($user_id);
        $modules_str =  isset($special['modules_str'])  ? $special['modules_str'] : '' ;
        $special_modules = array();
        if(!empty($modules_str)){
            $special_modules = json_decode($modules_str,true);            
        }
        $allUrls = array_merge($module_url,$special_modules);
        array_walk($allUrls, function(&$value)
        {
          $value = strtolower($value);
        });
        $is_super = isset($data['is_super'])?$data['is_super']:'';
        return array('allUrls' =>$allUrls , 'is_super' => $is_super )  ;
    }
    //获取 模块导航
    public static function getNav($uid){
        $res = self::getUserModules($uid);
        $allUrls = $res['allUrls'];
        $is_super = $res['is_super'];
        $allMenu = include_once APP_PATH."/conf/menu.php";
        if( $is_super == 1 ){
            return $allMenu;
        }
        foreach ($allMenu as $key => $value) {
            if( !in_array(strtolower($value['modular']) , $allUrls ) ){
                unset($allMenu[$key]);
            }else{
                if( !empty($value['child']) ){
                    foreach ($value['child'] as $skey => $svalue) {
                        if( !in_array( strtolower($svalue['modular']) , $allUrls ) ){
                            unset($allMenu[$key]['child'][$skey]);
                        }
                    }
                } 
            }
        }
        return $allMenu ;
        echo "<pre>";
        print_r($allMenu);
        print_r($allUrls);
        die();
    }
}