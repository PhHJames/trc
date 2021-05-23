<?php
/**
 * Created by 不要复制我的代码.
 * User: 不要复制我的代码
 * Date: 2019/5/14 0014
 * Time: 上午 11:45
 */
class CollectUserModel extends AbstractModel{
    protected $table = "collect_user" ;
    protected $dbIndex = 0  ;
    public function getDataByUsernamePwd($username ,$passwd){
        $sql = "select * from collect_user where account = :account and password = :password limit 1 ";
        return $this->db(0)->findOne($sql , array('account' => $username , 'password' => $passwd) );
    }
    //根据用户的id查询用户
    public function getCollectUserByUserId($user_id){
        $sql = "select * from collect_user where user_id = :user_id  limit 1 ";
        return $this->db(0)->findOne($sql , array('user_id' => $user_id ) );
    }
    public function execSql($sql){
        return $this->db($this->dbIndex)->Exec( $sql);
    }
}