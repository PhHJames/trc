<?php
/**
 * @Author: Awe
 * @Date:   2018-10-22 11:26:22
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-07 15:01:23
 */
class AdminUserModel extends AbstractModel{
    public function getDataByUsernamePwd($username ,$passwd){
        $sql = "select * from admin_user where username = :username and passwd = :passwd limit 1 ";
        return $this->db(0)->findOne($sql , array('username' => $username , 'passwd' => $passwd) );
    }
    //根据用户的id查询用户
    public function getAdminUserByUserId($user_id){
        $sql = "select * from admin_user where user_id = :user_id  limit 1 ";
        return $this->db(0)->findOne($sql , array('user_id' => $user_id ) );
    }
    public function getAdminUserByUserName( $username ){
        $sql = "select * from admin_user where username = :username  limit 1 ";
        return $this->db(0)->findOne($sql , array('username' => $username ) );
    }
    public function insertAdminUser($insert){
        $insert['addtime'] = date("Y-m-d H:i:s" ,time());
        return $this->db(0)->Insert("admin_user",$insert);
    }
    public function updateAdminUser($update,$where ){
        return $this->db(0)->Update("admin_user",$update,$where);
    }
    //获取用户的特殊权限
    public function getUserSpecial($user_id){
        $sql = "select * from admin_user_special_power where user_id = :user_id  limit 1 ";
        return $this->db(0)->findOne($sql , array('user_id' => $user_id ) );
    }
    //获取用户的特殊权限数组
    public function getUserSpecialArray($user_id){
        $data = self::getUserSpecial($user_id);
        if( empty($data) ){
            return array();
        }
        $module_url = $data['modules_str'];
        return json_decode($module_url,true);
    }
}