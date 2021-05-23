<?php
/**
 * @Author: Awe
 * @Date:   2018-05-31 11:21:18
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-10-12 12:14:46
 */
class Source_User{
    public static  $redis_user_key = "bird_user_" ;
    public static  $redis_user_db = 1 ;
    public static function getUserByUid($uid = 0 ){
        if( $uid <= 0 ){
          return array();
        }
        $redisHandler = RedisDB::getInstance(self::$redis_user_db)->handler;
        $user = $redisHandler->hGetAll(self::$redis_user_key.$uid);
        return $user ;
    }
    //修改redis的用户信息
    public static function updateRedisUser($user_id,$update){
        $redisHandler = RedisDB::getInstance(self::$redis_user_db)->handler;
        $status = $redisHandler->hMset("bird_user_{$user_id}" , $update  );
        if( !$status){
            return ;
        }
        return true ;
    }
    //删除redis的用户信息
    public static function deleteRedisUser($uid = 0){
        if( $uid <= 0 ){
          return false;
        }
        $redisHandler = RedisDB::getInstance(self::$redis_user_db)->handler;
        return $redisHandler->delete(self::$redis_user_key.$uid);
    }
    //删除用户的token
    public static function deleteUserToken($token){
        if( empty($token) ){
            return false ;
        }
        $redisHandler = RedisDB::getInstance(0)->handler;
        return $redisHandler->delete($token);
    }
    //生成一个比较场的的uid
    public static function makeUid($uid){
        return  intval($uid + 100000) ;
    }
    //根据长的ID 还原用户的id
    public static function getRealUid($show_user_id){
        return  intval($show_user_id  -  100000) ;
    }
}