<?php
class Admin_LoginController extends Admin_CommonController {
	public function init() {
		parent::init();
	}
	public function indexAction(){
	   /* $Tron_Address_Address = new Tron_Address_Address();
        echo $Tron_Address_Address->hexString2Address("410976782e1b25c5364ea68595145dd20bd9137276");*/
        if($this->isLogin){
            header("Location:" . Common::U("Admin_index/index") );
            exit;
        }
        if($this->getRequest()->isPost()){
            $this->dologin();
            exit;
        }
        $this->displayTemplate("login/index");
	}
    private function dologin(){
        $username = trim($this->getRequest()->getPost("username" , ""));
        $password = trim($this->getRequest()->getPost("password" , ""));
        if( empty($username) or empty($password)  ){
            Common::EchoResult(-3 , "params is error") ;
        }
        $adminUser = Common::ImportBusiness("AdminUser"); 
        $res = $adminUser->findUser($username , $password);
        if(!$res['code']){
            Common::EchoResult(0, $res['msg']) ;
        }
        $res = $adminUser->setUserCookie($res['info']['user_id']);
        if(!$res['code']){
            Common::EchoResult(0, $res['msg']) ;
        }
        Common::EchoResult(1, "OK") ;
    }
    //退出登录
    public function loginOutAction(){
        $this->delCookieVal();
        header("Location:" . Common::U("Admin_login/index") );
    }
}