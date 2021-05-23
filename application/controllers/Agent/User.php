<?php
/**
 * @Author: www.不要复制我的代码不然后果自负.com
 * @Date:   2016-06-13 11:46:15
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-10-31 16:51:02
 * @备注 此文件是用户登录之后就允许操作的 不设计到权限
 */
class Agent_UserController extends Agent_CenterController {
    public function init() {
        parent::init();
    }
    public function baseAction(){
        if($this->getRequest()->isPost()){
            $this->doEditbase();
            exit;
        }
        $MerchantModel = new MerchantModel();
        $info = $MerchantModel->getInfo( array('mid' => $this->userInfo['mid']) );
        $res = array('info' => $info );
        $this->displayTemplate("user/editbase" , $res );
    }
    private function doEditbase(){
        $params = $this->getParams();
        $appsecret = isset($params['appsecret'])?trim($params['appsecret']):'';
        $notify_url = isset($_POST['notify_url'])?trim($_POST['notify_url']):'';
        $supplement_url = isset($_POST['supplement_url'])?trim($_POST['supplement_url']):'';
        $stringClass = new StringClass();
        $MerchantModel = new MerchantModel();
        $info = $MerchantModel->getInfo( array('mid' => $this->userInfo['mid']) );
        if( !empty($appsecret) ){
            $len = $stringClass->abslength($appsecret);
            if( $len != 32 ){
                Common::EchoResult(-3 , "秘钥长度必须是32个英文字符");
            }
        }

        if( !empty($notify_url) ){
            if( $info['notify_url'] !=  $notify_url ){
                $header = @get_headers($notify_url);
                if( empty($header )){
                    $header = [] ;
                }
                if(!preg_grep("/200/", $header)){
                    Common::EchoResult(-3 , "URL地址无法访问,请填写正确的URL地址");
                }
            }
        }else{
            Common::EchoResult(-3 , "请输入异步回调地址");
        }

        if( !empty($supplement_url) ){
            if( $info['supplement_url'] !=  $supplement_url ){
                $header = get_headers($supplement_url);
                if(!preg_grep("/200/", $header)){
                    Common::EchoResult(-3 , "补单URL地址无法访问,请填写正确的URL地址");
                }
            }
        }else{
            Common::EchoResult(-3 , "请输入补单异步回调地址");
        }
        $request = array(
            'appsecret' => $appsecret,
            'notify_url' => $notify_url ,
            'supplement_url' => $supplement_url
        );
        $msg = "基本信息设置成功" ;
        if( empty($appsecret) ){
            unset($request['appsecret']);
        }else{
            if( $info['appsecret'] !=  $appsecret ){
                $msg .= " , 秘钥已经重置为：<br /><font color='red'>" .  $appsecret . "</font>   <br /> 请牢记秘钥 不要泄露"  ;
            }
        }
        $request['update_time'] = date("Y-m-d H:i:s" , time() );
        $MerchantModel->updateData( $request ,array('mid' =>$this->userInfo['mid'] ) );
        Common::EchoResult(1,  $msg  );
    }
    public function editpasswdAction(){
        if($this->getRequest()->isPost()){
            $this->doEditpasswd();
            exit;
        }
        $res = array();
        $this->displayTemplate("user/editpasswd" , $res );
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
            'agent_id'=>$this->userInfo['agent_id'],
        );
        $Business = Common::ImportBusiness("Agent" , "Agent" );
        try{
            $Business->editPasswd( $request);
            $this->delCookieVal();
            Common::EchoResult(1 ,"修改成功" );
        }catch(Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage());
        }
    }
}