<?php
//后台的基类控制器
class Admin_CommonController extends Yaf_Controller_Abstract {
    public $userInfo = array();
    public $isLogin = false ;
    public $current_url = '';
	public function init() {


        $this->checkAuth();
        $this->assignVariable();
        $this->initLogin();
	}
    //assing 分配变量了
    public function assignVariable(){
        global $_G; 
        $dispatcher = Yaf_Dispatcher::getInstance();
        $request = $dispatcher->getRequest() ;
        $url = strtolower($request->controller)."/".strtolower($request->action);
        $this->current_url = $_G['config']['domain']['domain']['ADMIN_DOMAIN'] . $url ;
        $_G['viewObject']->assign("current_url",$this->current_url);
        $_G['viewObject']->assign("MODULE_URL",$url);
    }
    public function initLogin(){
        global $_G;
        $adminAuth = $this->getCookeVal();
        $authString = Encrypt::AuthCode($adminAuth,"DECODE",$_G['config']['encrypt']['encrypt']['key']);
        $authData = @unserialize($authString);
        if($authData['user_id'] > 0 && $authData['username']){
            $this->isLogin = true ;
            $this->userInfo = $authData;
        }
    }
    //获取cookie的值
    public function getCookeVal(){
        global $_G;
        $cookie_name ="bcr_admin_auth";
        $adminAuth = trim(Common::Dhtmlspecialchars(isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : '' ));
        return $adminAuth ;
    }
    //删除cookie的值
    public function delCookieVal(){
        global $_G ;
        $cookie_name = "bcr_admin_auth";
        if(isset($_COOKIE[$cookie_name])){
            setcookie($cookie_name, '', time() - 3600, "/");
            unset($_COOKIE[$cookie_name]); 
        }
        return true ;
    }
    //分配到模版的变量名称
    public function displayTemplate($file = null , $args = array()){
        global $_G;
        if($args && !empty($args) ){
            foreach($args as $key => $val ){
               $_G['viewObject']->assign($key,$val);
            }
        }
        $_G['viewObject']->display("admin/" . $file.".htm");
    }
    //显示错误页面,并且根据时间进行跳转
    public function error($message , $url , $time = 3){
        $this->displayTemplate("public/error",array('info'=>$message,'url'=>$url ,'time'=>$time));
        exit;
    }
    //设置成功页面根据时间进行跳转
    public function success($message , $url , $time = 3){
        $this->displayTemplate("public/success",array('info'=>$message,'url'=>$url ,'time'=>$time));
        exit;
    }
    //直接显示错误信息
    public function showError($head = '' , $message = ''){
       $this->displayTemplate("public/show_error",array('head'=>$head,'message'=>$message )); 
       exit;
    }
    //根据请求类型显示不同的错误信息 或者是页面等,调用此方法会直接断点输出数据
    public function displayError($msg = '' , $code =  0 ){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            Common::EchoResult($code  , $msg);
        }
        $this->showError("系统提示" , $msg);
    }
    //获取参数并且过滤
    //1:获取_request 2:获取post 3：获取get
    public function getParams($type = 1 ){
        switch ($type){
            case 1 :
            $request_data = $_REQUEST ;
            break;
            case 2:
            $request_data = $_POST ;
            default:
            $request_data = $_GET ;
        }
        $request_data = Common::Dhtmlspecialchars(Common::Dtrim($request_data));
        return $request_data ;
    }
    //输出列表的数据
    /**
    * 输出列表的数据json
    * $code int  返回的错误码
    * $msg  string 返回的错误信息
    * $count  int 数据总数
    * $data array 返回的数组
    */
    public function echoListJson($code = 0 ,$msg = "" ,$count = 0 ,$data  = array() ){
        $result = array(
            'code' => $code ,
            'msg' => $msg ,
            'data' => $data,
            'count' =>  intval($count) 
        );
        echo json_encode($result ,  JSON_UNESCAPED_UNICODE);
        exit;
    }
    /**
     *  写入操作日志
     * $code int  返回的错误码
     * $msg  string 返回的错误信息
     * $count  int 数据总数
     * $data array 返回的数组
     */
    public function writeActionLog( $name = "" , $describe = "" ){
        $member_id =  isset($this->userInfo['user_id']) ? $this->userInfo['user_id'] : 0 ;
        $username =  isset($this->userInfo['username']) ? $this->userInfo['username'] : "" ;
        $dispatcher = Yaf_Dispatcher::getInstance();
        $request = $dispatcher->getRequest() ;
        $url = strtolower($request->controller)."/".strtolower($request->action);
        $ip = Network::GetClientIp();
        $res = array(
            'member_id' => $member_id ,
            'username' => $username ,
            'ip' => $ip ,
            'name' => $name ,
            'describe' => $describe ,
            'url' => $url ,
            'create_time' => date("Y-m-d H:i:s"),
            'request' => json_encode($_REQUEST , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
        );
        $AdminLogModel = new AdminLogModel();
        $AdminLogModel->insertData($res);
    }
    /**
     *  检测认证
     */
    public function checkAuth(){
        global $_G;
        $user = $_G['config']['setting']['admin_user'];
        $password =$_G['config']['setting']['admin_password'];
        // 通过HTTP认证进行验证
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] != $user || $_SERVER['PHP_AUTH_PW'] != $password) {
            header('WWW-Authenticate: Basic realm="Test auth"');
            header('HTTP/1.0 401 Unauthorized');
            $this->displayError("认证失败 请重新输入用户名和密码进行认证....." , 0 );
            /*echo 'Auth failed';
            exit;*/
        }
        /*$auth = isset($_COOKIE['admin_is_auth']) ? $_COOKIE['admin_is_auth'] : 0 ;
        if($auth != 1 ){
            $this->displayError("你暂时未认证无法访问....." , 0 );
        }*/
    }

}