<?php
/**
 * @Author: www.不要复制我的代码.com
 * @Date:   2016-06-13 11:46:15
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-10-31 16:51:02
 * @备注 此文件是用户登录之后就允许操作的 不设计到权限
 */
class Admin_UserController extends Admin_CenterController {
    public function init() {
        parent::init();
    }

    //用户修改密码
    public function editpasswdAction(){
        if($this->getRequest()->isPost()){
            $this->doEditpasswd();
            exit;
        }
        $res = array();
        $this->displayTemplate("admin/editpasswd" , $res );
    }
    private function doEditpasswd(){
        $params = $this->getParams();
        $oldPassword = isset($params['oldPassword'])?trim($params['oldPassword']):'';
        $password = isset($params['password'])?trim($params['password']):'';
        $repassword = isset($params['repassword'])?trim($params['repassword']):'';
        if( empty($oldPassword) ){
            Common::EchoResult(-3 , "请输入老密码");
        }
        if( empty($password) ){
            Common::EchoResult(-3 , "请输入新密码");
        }
        if( empty($repassword) ){
            Common::EchoResult(-3 , "请输入重复密码");
        }
        
        $StringClass = new StringClass();
        
        if($StringClass->utf8_str($oldPassword) != 1 ){
            Common::EchoResult(-3 , "密码必须是英文字符");
        }
        $passwordstrLen = $StringClass->abslength($oldPassword) ;
        if($passwordstrLen < 6 or $passwordstrLen > 16 ){
            Common::EchoResult(-3 , "密码必须是6-16位英文字符");
        }

        if($StringClass->utf8_str($password) != 1 ){
            Common::EchoResult(-3 , "密码必须是英文字符");
        }
        $passwordstrLen = $StringClass->abslength($password) ;
        if($passwordstrLen < 6 or $passwordstrLen > 16 ){
            Common::EchoResult(-3 , "新密码必须是6-16位英文字符");
        }
        if($password != $repassword ){
            Common::EchoResult(-3 , "2次密码必须相同");
        }
        $request = array(
            'oldPassword' => $oldPassword ,
            'passwd' => $password,
            'user_id'=>$this->userInfo['user_id'],
        );
        $Business = Common::ImportBusiness("AdminUser" );
        $data = $Business->editPasswd($request );
        Common::EchoResult($data['code'] , $data['msg']);
    }
}