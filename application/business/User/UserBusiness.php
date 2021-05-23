<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 * @des 后台的用户 业务文件
 */
class UserBusiness extends AbstractModel {
    public function findUser($username , $password){
        $adminUser = new AdminUserModel();
        $password = md5($password);
        $info = $adminUser->getDataByUsernamePwd($username , $password);
        if(!$info || !is_array($info)){
            return array('code' => 0 , 'msg' =>"用户名或者是密码错误") ;
        }
        if($info['status'] == 2 ){
            return array('code' => 0 , 'msg'=>'此用户已经被禁用');
        }
        if($info['status'] == 0 ){
            return array('code' => 0 , 'msg'=>'审核中。。。。');
        }
        //修改字段登录次数
        $this->db(0)->Exec("update `admin_user` set login_num = login_num + 1 where username = :username " ,array('username'=>$username));
        return array('code' => 1 , 'msg'=>'success' , 'info'=>$info) ;
    }
    //写入登录状态,这个是针对 权限管理系统的登录帐号
    public function setUserCookie($user_id){
        $AdminUserModel = new AdminUserModel();
        $userInfo = $AdminUserModel->getAdminUserByUserId($user_id);
        if( empty($userInfo) ){
            return false;
        }
        global $_G ;
        $cookieData = array();
        $cookieData['nick'] = ($userInfo['nick'])  ? $userInfo['nick']:$userInfo['username'] ;
        $cookieData['user_id'] = $userInfo['user_id'];
        $cookieData['username'] = $userInfo['username'];
        $cookieData['super'] = $userInfo['super'];
        $data_string = serialize($cookieData) ;
        $data_string = Encrypt::AuthCode($data_string , "ENCODE" ,$_G['config']['encrypt']['encrypt']['key']);
        setcookie("bcr_admin_auth", $data_string, time()+intval(3600*72), "/");
        return array('code' => 1 , 'message'=>'success');
    }
    public function getUserList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getUserWhere($params);
        $sql = "select a.*  from user as a   {$getWhere} order by a.addtime desc "   ;
        $sql_count = "select count(*) as c   from user as a  {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getUserWhere($params=array()){
        $getWhere = "";
        if(isset($params['user_id']) && $params['user_id']){
            $getWhere.=" and a.`user_id` = '{$params['user_id']}' ";
        }
        if(isset($params['status']) && $params['status']){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        return $getWhere;
    }
    //根据用户的id查询用户所属的权限模块
    //返回数组的形式
    public function getUserPrivilegsModules($user_id){
        $AdminUserModel = new AdminUserModel();
        $RoleModel = new RoleModel();
        $info = $AdminUserModel->getAdminUserByUserId($user_id);
        if( empty($info) ){
            return array('code' => 0 , 'msg' => "error,没有查询到用户" );
        }
        if( $info['super']  == 1 ){//管理员
            return array('code' => 1 , 'msg' => "OK" , 'is_super' => 1  );
        }
        if( empty($info['role_ids']) ){
            return array('code' => 1 , 'msg' => "OK" , 'is_super' => 0 , 'module_url' => array()  );
        }
        $role_id_data = explode("," , $info['role_ids']);
        $module_url = $RoleModel->getBeatchUrlByRoleId($role_id_data);
        return array('code' => 1 , 'msg' => "OK" , 'is_super' => 0 , 'module_url' => $module_url );
    }
    //添加后台用户
    public function addUser( $params ){
        $username = $params['username'];
        $passwd = $params['passwd'];
        $nick = $params['nick'];
        $super = $params['super'];
        $params['passwd'] = md5($params['passwd']);
        $model = new AdminUserModel();
        $info = $model->getAdminUserByUserName($username);
        if( !empty($info) ){
            return array('code' => 0 , 'msg' => "帐号已经存在！！" );
        }
        $user_id = $model->insertAdminUser($params);
        if( !$user_id ){
            return array('code' => 0 , 'msg' => "添加失败请稍后" );
        }
        return array('code' => 1 , 'msg' => "添加成功");
    }
    public function editUser( $params ){
        $nick = $params['nick'];
        $user_id = $params['user_id'];
        $role_id = $params['role_id'];
        $super = $params['super'];
        $modules = $params['modules'];
        $status = $params['status'];
        $passwd = $params['passwd'];
        $model = new AdminUserModel();
        $info = $model->getAdminUserByUserId($user_id);
        if( empty($info) ){
            return array('code' => 0 , 'msg' => "帐号不存在！！" );
        }
        $role_ids = '';
        if( $super == 1 ){
            $role_ids = '';
        }else{
            if( !empty($role_id) ){
                $role_ids = implode(",",$role_id);
            }
        }
        $updateUser = array(
            'nick' => $nick,
            'super' => $super ,
            'role_ids' => $role_ids,
            'status' => $status
        );
        if( $passwd ){
            $updateUser['passwd'] = md5($passwd);
        }
        $model->updateAdminUser($updateUser,array('user_id' => $user_id ));
        if( !empty($modules) ){
            $modules_str =  json_encode($modules,JSON_UNESCAPED_UNICODE);
            $sql="insert into admin_user_special_power (user_id,modules_str)  values('{$user_id}','{$modules_str}') on  DUPLICATE key update modules_str = '{$modules_str}' ";
            $this->db(0)->Exec($sql);
        }
        return array('code' => 1 , 'msg' => "修改成功");
    }
    public function deleteUser( $user_id ){
        $sql = "delete from admin_user where user_id = $user_id ";
        $this->db(0)->Exec($sql);
        $sql = "delete from admin_user_special_power where user_id = $user_id ";
        $this->db(0)->Exec($sql);
        return true ;
    }
    public function editPasswd($req ){
        $oldPassword = $req['oldPassword'];
        $user_id = $req['user_id'];
        $passwd = $req['passwd'];
        $AdminUserModel = new AdminUserModel();
        $userInfo = $AdminUserModel->getAdminUserByUserId($user_id);
        if( $userInfo['passwd'] != md5($oldPassword)  ){
            return array('code' => 0 ,'msg' => "你的旧密码不对" );
        }
        if($oldPassword == $passwd ){
            return array('code' => 0 ,'msg' => "2次密码不可以一样" );
        }
        $update = array('passwd' => md5($passwd));
        $status = $AdminUserModel->updateAdminUser($update,array('user_id' => $user_id ));
        if( !$status ){
            return array('code' => 0 ,'msg' => "网络繁忙请稍后" );
        }
        return array('code' => 1,'msg' => "修改成功" );
    }
}