<?php
class Collect_RegisterController extends Collect_CommonController {
    public function init() {
        parent::init();
        exit("暂不开放");
    }
    public function indexAction(){
        if($this->isLogin){
            header("Location:/Collect_index/index");
            exit;
        }
        if($this->getRequest()->isPost()){
            $this->doregister();
            exit;
        }
        $this->displayTemplate("register/index");
    }
    private function doregister(){
        $params = $this->getParams() ;
        $Business = Common::ImportBusiness("User" , "Collect");
        try{
            $info = $Business->doregister($params);
            Common::EchoResult(1, "OK") ;
        }catch(Exception $e ){
            Common::EchoResult($e->getCode(), $e->getMessage()) ;
        }
    }
    //退出登录
    public function loginOutAction(){
        $this->delCookieVal();
        header("Location:/Collect_login/index" );
    }
}