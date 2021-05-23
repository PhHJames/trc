<?php
class Collect_LoginController extends Collect_CommonController {
	public function init() {
		parent::init();
	}
	public function indexAction(){
        if($this->isLogin){
            header("Location:/Collect_index/index");
            exit;
        }
        if($this->getRequest()->isPost()){
            $this->dologin();
            exit;
        }
        $this->displayTemplate("login/index");
	}
    private function dologin(){
        $account = trim($this->getRequest()->getPost("account" , ""));
        $password = trim($this->getRequest()->getPost("password" , ""));
        if( empty($account) or empty($password)  ){
            Common::EchoResult(-3 , "params is error") ;
        }
        $Business = Common::ImportBusiness("User" , "Collect");
        try{
            $info = $Business->findUser($account , $password);
        }catch(Exception $e ){
            Common::EchoResult($e->getCode(), $e->getMessage()) ;
        }
        $res = $Business->setUserCookie($info['user_id']);
        if( $res === false ){
            Common::EchoResult(0, "登录失败" ) ;
        }
        Common::EchoResult(1, "OK") ;
    }
    //退出登录
    public function loginOutAction(){
        $this->delCookieVal();
        header("Location:/Collect_login/index" );
    }
}