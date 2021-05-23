<?php
class Collect_CenterController extends Collect_CommonController {
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
            header("Location:" . "/Collect_login/index");
            exit;
        }
        $this->checkStatus();
    }

    public function checkStatus(){
	    $user_id = $this->userInfo['user_id'];
	    $CollectUserModel = new CollectUserModel();
        $info = $CollectUserModel->getInfo(['user_id' => $user_id]);
        if( !isset($info['status']) or  $info['status'] != 1 ){
            $this->showError("系统提示" , "你的账号被禁用或者审核未通过");
        }
    }
    //目前先写死了
    public function getMenu(){
        $menuConfig = include_once APP_PATH."/conf/collect_menu.php";
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
