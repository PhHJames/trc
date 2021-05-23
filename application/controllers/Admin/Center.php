<?php
//后台的个人中心 这个控制器只需要登录之后才可以操作的
class Admin_CenterController extends Admin_CommonController {
	public function init() {
		parent::init();
        $this->isLogin();
	}
    public function isLogin(){
        if(isset($this->NotLoginExclusion) AND $this->isIn($this->NotLoginExclusion)){
            return ;
        }
        if(!$this->isLogin){
            if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
                Common::EchoResult(-9  , "你的登录状态已经失效,请重新登录");
            }
            header("Location:" . Common::U("admin_login/index"));
            exit;
        }
    }
    //目前先写死了
    public function getMenu(){
        $menuConfig = include_once APP_PATH."/conf/menu.php";
        return $menuConfig;
    }
    
    //是否在白名单里面 如果在 那么不验证
    public function isIn($exclude = array() ){
        if(empty($exclude)){
            return false;
        }
        $action = strtolower($this->getRequest()->getActionName());
        foreach ($exclude as $item) {
            if (strtolower($item) === $action) {
                return true ;
            }
        }
        return false ;
    }
}
