<?php
//APi
class Shell_CommonController extends Yaf_Controller_Abstract {
    public $current_url = '';
	public function init() {
        Yaf_Dispatcher::getInstance()->disableView();
        $request = new Yaf_Request_Simple();
        if(strtoupper($request->method)!= 'CLI'){
            exit("error visit please use cli ");
        }
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

}