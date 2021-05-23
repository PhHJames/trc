<?php
/**
 * @Author: Awe
 * @Date:   2016-05-20 20:17:10
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-10-30 16:38:45
 */
class Admin_BaseAuthController extends Admin_CenterController{
    public function init() {
        parent::init();
        if(isset($this->NotAuthExclusion) AND $this->isIn($this->NotAuthExclusion)){
            return ;
        }
        $this->initAuth();
    }
    //初始化权限
    private  function initAuth(){
        $url = $this->getCurrentUrl();
        $res = Rbac::GETPermission($url , $this->userInfo['user_id']);
        //echo $url;
        if($res['is_super'] != 1 ){
            if(!in_array($url , $res['module_url'])){
                $this->displayError("你目前没有操作权限，或者是你退出登录看看有没权限哈" , -8);
            }
        }
    }
    //获取当前的url地址
    private function getCurrentUrl(){
        //注意这个地方把当前的控制器转化为了小写，方法也转化了小写，请注意
        //权限那个地方添加的时候请注意
        $dispatcher = Yaf_Dispatcher::getInstance();
        $request = $dispatcher->getRequest() ;
        $url = strtolower($request->controller)."/".strtolower($request->action);
        return $url ;
    }
}
