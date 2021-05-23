<?php
//后台的基类控制器
class Merch_CommonController extends Yaf_Controller_Abstract {
    public $userInfo = array();
    public $isLogin = false ;
    public $current_url = '';
	public function init() {
        $this->checkCommon();
        $this->assignVariable();
        $this->initLogin();

	}
	public function checkCommon(){
	    $is_close_plat = Source_Sysconfig::getInstance()->getVal("is_close_plat");
	    if($is_close_plat == 'Y' ){
	        $this->showError("系统提示"  , Source_Sysconfig::getInstance()->getVal("plat_close_message") );
        }
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
        //print_r(json_decode($_G['config']['currency']['channel_name']));
    }
    public function initLogin(){
        global $_G;
        $adminAuth = $this->getCookeVal();
        $authString = Encrypt::AuthCode($adminAuth,"DECODE",$_G['config']['encrypt']['encrypt']['key']);
        $authData = @unserialize($authString);
        if($authData['mid'] > 0 ){
            $this->isLogin = true ;
            $this->userInfo = $authData;
        }
    }
    //获取cookie的值
    public function getCookeVal(){
        global $_G;
        $cookie_name ="merch_admin_auth";
        $adminAuth = trim(Common::Dhtmlspecialchars(isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : '' ));
        return $adminAuth ;
    }
    //删除cookie的值
    public function delCookieVal(){
        global $_G ;
        $cookie_name = "merch_admin_auth";
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
        $_G['viewObject']->display( "merch/" . $file.".htm");
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
}