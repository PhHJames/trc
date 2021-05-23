<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_User_UserController extends Admin_BaseAuthController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxUserList();
            exit;
        }
        $res = array(
            'pageSize'=> 10 
        );
        $this->displayTemplate('user/user_index' , $res);
    }
    private function getAjaxUserList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("User" ,"User" );
        $data = $Business->getUserList($params,$pageSize);
        $RoleModel = new RoleModel();
        $list = $data['list'];
        if( !empty($list) ){
            foreach ($list as $key => $value) {
                $role_str = '';
                $list[$key]['mobile'] = $value['mobile'] ? $value['mobile'] :'';
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
    public function userAddAction(){
        if($this->getRequest()->isPost()){
            $this->doAddUser();
            exit;
        }
        $menu = $this->getMenu();
        $RoleModel = new RoleModel();
        $allRole = $RoleModel->getAllRole(" AND status = 1 ");
        $res = array(
            'menu' => $menu ,
            'allRole' => $allRole,
        );
        $this->displayTemplate('admin/user_add' , $res);
    }
    private function doAddUser(){
        $params = $this->getParams();
        $username = isset($params['username'])?trim($params['username']):'';
        $passwd = isset($params['passwd'])?trim($params['passwd']):'';
        $nick = isset($params['nick'])?trim($params['nick']):'';
        $super = isset($params['super'])?trim($params['super']):'';
        $role_id = isset($_POST['role_id'])?($_POST['role_id']):'';
        if( empty($username) ){
            Common::EchoResult(-3 , "请输入帐号");
        }
        if( empty($passwd) ){
            Common::EchoResult(-3 , "请输入密码");
        }
        if( empty($nick) ){
            Common::EchoResult(-3 , "请输入昵称");
        }
        if( empty($super) ){
            Common::EchoResult(-3 , "请选择是否是管理员");
        }
        $StringClass = new StringClass();
        if($StringClass->utf8_str($username) != 1 ){
            Common::EchoResult(-3 , "登录帐号必须是英文字符");
        }
        $UserNamestrLen = $StringClass->abslength($username) ;
        if($UserNamestrLen < 6 or $UserNamestrLen > 16 ){
            Common::EchoResult(-3 , "登录帐号必须是6-16位英文字符");
        }
        if($StringClass->utf8_str($passwd) != 1 ){
            Common::EchoResult(-3 , "密码必须是英文字符");
        }
        $passwordstrLen = $StringClass->abslength($passwd) ;
        if($passwordstrLen < 6 or $passwordstrLen > 16 ){
            Common::EchoResult(-3 , "密码必须是6-16位英文字符");
        }
        $role_ids = '';
        if($super == 2 ){
            if( empty($role_id ) ){
                Common::EchoResult(-3 , "请选择权限");
            }
            $role_ids =  implode("," ,$role_id );
        }
        $request = array(
            'username' => $username ,
            'passwd' => $passwd,
            'nick'=>$nick,
            'super' => $super,
            'role_ids'=>$role_ids,
        );
        $Business = Common::ImportBusiness("AdminUser" );
        $data = $Business->addUser($request );
        Common::EchoResult($data['code'] , $data['msg']);
    }
    public function userEditAction(){
        if($this->getRequest()->isPost()){
            $this->doEditUser();
            exit;
        }
        $params = $this->getParams();
        $user_id = isset($params['user_id']) ? $params['user_id'] : 0 ;
        $RoleModel = new RoleModel();
        $AdminUserModel = new AdminUserModel();
        $info = $AdminUserModel->getAdminUserByUserId($user_id);
        $role_ids = explode(",",$info['role_ids']);

        $menu = $this->getMenu();
        $allRole = $RoleModel->getAllRole(" AND status = 1 ");
        $special = $AdminUserModel->getUserSpecialArray($user_id);
        $res = array(
            'menu' => $menu ,
            'allRole' => $allRole,
            'info' => $info,
            'role_ids'=>$role_ids,
            'allModeules'=>$special
        );
        $this->displayTemplate('admin/user_edit' , $res);
    }
    private function doEditUser(){
        $params = $this->getParams();
        $user_id = isset($params['user_id'])?trim($params['user_id']):'';
        $nick = isset($params['nick'])?trim($params['nick']):'';
        $super = isset($params['super'])?trim($params['super']):'';
        $status = isset($params['status'])?trim($params['status']):'';
        $passwd = isset($params['passwd'])?trim($params['passwd']):'';
        $role_id = isset($_POST['role_id'])?($_POST['role_id']):'';
        $modules = isset($_POST['modules'])?($_POST['modules']):'';
        if( empty($user_id) ){
            Common::EchoResult(-3 , "非法访问");
        }
        if( empty($nick) ){
            Common::EchoResult(-3 , "请输入昵称");
        }
        $StringClass = new StringClass();
        if( !empty($passwd) ){
            if($StringClass->utf8_str($passwd) != 1 ){
                Common::EchoResult(-3 , "密码必须是英文字符");
            }
            $passwordstrLen = $StringClass->abslength($passwd) ;
            if($passwordstrLen < 6 or $passwordstrLen > 16 ){
                Common::EchoResult(-3 , "密码必须是6-16位英文字符");
            }
        }
        
        if($super != 1 ){
            if( empty($role_id) AND empty($modules) ){
                Common::EchoResult(-3 , "你传递角色id");
            }
            if( !empty($modules) ){
                if( !is_array($modules) ){
                    Common::EchoResult(-3 , "角色的格式错误");
                }
            }
        }
        //print_r($modules);die();
        $Business = Common::ImportBusiness("AdminUser" );
        $request = array(
            'nick'=>$nick,
            'super' => $super,
            'role_id'=>$role_id,
            'modules'=>$modules,
            'user_id'=>$user_id,
            'status' => $status,
            'passwd'=>$passwd
        );
        $data = $Business->editUser($request,$user_id);
        Common::EchoResult($data['code'] , $data['msg']);
    }
    public function userDelAction(){
        $params = $this->getParams();
        $user_id = isset($params['user_id'])?trim($params['user_id']):'';
        if( empty($user_id) ){
            Common::EchoResult(-3 , "非法访问");
        }
        if($this->userInfo['user_id'] == $user_id ){
            Common::EchoResult(-3 , "不可以删除自己");
        }
        //print_r($modules);die();
        $Business = Common::ImportBusiness("AdminUser" );
        $data = $Business->deleteUser($user_id);
        Common::EchoResult(1, "删除成功");
    }
    public function roleAction(){
        $res = array(
            'pageSize'=> 10 
        );
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxRoleList();
            exit;
        }
        $this->displayTemplate('admin/role' , $res);
    }
    private function getAjaxRoleList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("Role" );
        $data = $Business->getRoleList($params,$pageSize);
        $this->echoListJson(0,"OK" ,$data['total'] ,$data['list'] );
    }
    //添加角色
    public function addRoleAction(){
        if($this->getRequest()->isPost()){
            $this->doAddRole();
            exit;
        }
        $menu = $this->getMenu();
        $res = array(
            'menu' => $menu 
        );
        $this->displayTemplate('admin/role_add' , $res);
    }
    private function doAddRole(){
        $params = $this->getParams();
        $role_name = isset($params['role_name'])?trim($params['role_name']):'';
        $remark = isset($params['remark'])?trim($params['remark']):'';
        $modules = isset($params['modules'])?($_POST['modules']):'';
        if( empty($role_name) ){
            Common::EchoResult(-3 , "请输入角色名称");
        }
        if( empty($modules) ){
            Common::EchoResult(-3 , "请选择角色");
        }
        if( !is_array($modules) ){
            Common::EchoResult(-3 , "角色的格式错误");
        }

        $Business = Common::ImportBusiness("Role" );
        $data = $Business->addRole($role_name,$modules,$remark);
        Common::EchoResult($data['code'] , $data['msg']);
    }
    //修改角色
    public function editRoleAction(){
        if($this->getRequest()->isPost()){
            $this->doEditRole();
            exit;
        }
        $params = $this->getParams();
        $roleid = isset($params['roleid']) ? $params['roleid'] : 0 ;
        $RoleModel = new RoleModel();
        $info = $RoleModel->getRoleByRoleId($roleid);
        if( empty($info) ){
            $this->showError("温馨提醒" ,"没有查询到相关的数据");
        }
        $allModeules = $RoleModel->getRolePrivilegsUrlByRoleId($roleid);
        $menu = $this->getMenu();
        $res = array(
            'menu' => $menu ,
            'info' => $info,
            'allModeules'=>$allModeules
        );
        $this->displayTemplate('admin/role_edit' , $res);
    }
    private function doEditRole(){
        $params = $this->getParams();
        $role_name = isset($params['role_name'])?trim($params['role_name']):'';
        $role_id = isset($params['role_id'])?trim($params['role_id']):'';
        $remark = isset($params['remark'])?trim($params['remark']):'';
        $modules = isset($params['modules'])?($_POST['modules']):'';
        if( empty($role_id) ){
            Common::EchoResult(-3 , "你传递角色id");
        }
        if( empty($modules) ){
            Common::EchoResult(-3 , "请选择角色");
        }
        if( !is_array($modules) ){
            Common::EchoResult(-3 , "角色的格式错误");
        }

        $Business = Common::ImportBusiness("Role" );
        $data = $Business->editRole($role_id,$role_name,$modules,$remark);
        Common::EchoResult($data['code'] , $data['msg']);
    }
}