<?php
//APi
class Api_CommonController extends Yaf_Controller_Abstract {
    public $current_url = '';
	public function init() {
        $this->assignVariable();
	}
    //assing 分配变量了
    public function assignVariable(){
        global $_G; 
        $dispatcher = Yaf_Dispatcher::getInstance();
        $request = $dispatcher->getRequest() ;
        $url = strtolower($request->controller)."/".strtolower($request->action);
        $this->current_url = $_G['config']['domain']['domain']['API_DOMAIN'] . $url ;
        $_G['viewObject']->assign("current_url",$this->current_url);
        $_G['viewObject']->assign("MODULE_URL",$url);
    }
    //分配到模版的变量名称
    public function displayTemplate($file = null , $args = array()){
        global $_G;
        if($args && !empty($args) ){
            foreach($args as $key => $val ){
               $_G['viewObject']->assign($key,$val);
            }
        }
        $_G['viewObject']->display( "api/" . $file.".htm");
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
            break ;
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
    public function echoJson($code = 0 ,$msg = ""  ,$data  = array() ){
        if( empty($data) ){
            $data = (Object)array() ;
        }
        $result = array(
            'code' => intval( $code ) ,
            'msg' => $msg ,
            'data' => $data,
        );
        echo json_encode($result ,  JSON_UNESCAPED_UNICODE);
        exit;
    }
}