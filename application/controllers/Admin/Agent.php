<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_AgentController extends Admin_BaseAuthController {
    public $NotAuthExclusion = [];
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxAgentList();
            exit;
        }
        $res = array(
            'pageSize'=> 10
        );

        $this->displayTemplate('agent/user_index' , $res);
    }
    private function getAjaxAgentList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("Agent" ,"Admin" );
        $data = $Business->getAgentList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            foreach ($list as $key => $value) {
                //$list[$key]['money'] = $value['money'] / 100 ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
    //查看代理商
    public function  previewAction(){
        $params = $this->getParams();
        $agent_id =  isset($params['agent_id']) ? intval($params['agent_id']) : 0  ;
        $AgentModel = new AgentModel();
        $info  = $AgentModel->getInfo( array('agent_id' => $agent_id ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $AgentChannelBusiness = Common::ImportBusiness("Agent" ,"Admin" );
        $channel = $AgentChannelBusiness->getChannelList( array('agent_id' =>$agent_id) );
        if( $channel ){
            foreach ($channel as $key => $val ){
                $channel[$key]['is_open'] = 1 ;
                if( empty($val['merch_channel'])){
                    $channel[$key]['is_open'] =  0  ;
                }else{
                    $fee = $val['merch_channel']['fee'] * 100 ;
                    $fee = Common::foramtNumber( $fee , 2 );
                    $channel[$key]['merch_channel']['fee'] = $fee;
                }
            }
        }
        /*echo "<pre>";
        print_r($channel);*/
        $res = array(
            'info' => $info,
            'channel' => $channel
        );
        $this->displayTemplate('agent/preview' , $res);
    }
    //资金操作
    public function  moneyAction(){
        $params = $this->getParams();
        if($this->getRequest()->isPost()){
            $this->doMoney();
            exit;
        }
        $mid =  isset($params['agent_id']) ? intval($params['agent_id']) : 0  ;
        $MerchantModel = new AgentModel();
        $info  = $MerchantModel->getInfo( array('agent_id' => $mid ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $res = array(
            'info' => $info,
        );
        $this->displayTemplate('agent/money' , $res);
    }

    private function doMoney(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("Agent" ,"Admin" );
        try{
            $res  = $Business->doMoney( $params);
            $this->writeActionLog("操作代理商的金额" , "操作代理商的金额" );
            Common::EchoResult(1 , "操作成功" );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }
    public function addAction(){
        if($this->getRequest()->isPost()){
            $this->doAddAgent();
            exit;
        }
        $res = array(

        );
        $this->displayTemplate('agent/agent_add' , $res);
    }
    private function doAddAgent(){
        $params = $this->getParams();
        $account = isset($params['account'])?trim($params['account']):'';
        $password= isset($params['password'])?trim($params['password']):'';
        $phone = isset($params['phone'])?trim($params['phone']):'';
        $name = isset($params['name'])?trim($params['name']):'';
        if( empty($name) ){
            Common::EchoResult(-3 , "请输入代理商名称");
        }
        if( empty($account) ){
            Common::EchoResult(-3 , "请输入帐号");
        }
        if( empty($password) ){
            Common::EchoResult(-3 , "请输入密码");
        }
        if( empty($phone) ){
            Common::EchoResult(-3 , "请输入手机号码");
        }
        if( !Tools::isMobile($phone)){
            Common::EchoResult(-3 , "请输入正确手机号码");
        }
        $StringClass = new StringClass();
        if($StringClass->utf8_str($account) != 1 ){
            Common::EchoResult(-3 , "登录帐号必须是英文字符");
        }
        $UserNamestrLen = $StringClass->abslength($account) ;
        if($UserNamestrLen < 6 or $UserNamestrLen > 16 ){
            Common::EchoResult(-3 , "登录帐号必须是6-16位英文字符");
        }
        if($StringClass->utf8_str($password) != 1 ){
            Common::EchoResult(-3 , "密码必须是英文字符");
        }
        $passwordstrLen = $StringClass->abslength($password) ;
        if($passwordstrLen < 6 or $passwordstrLen > 16 ){
            Common::EchoResult(-3 , "密码必须是6-16位英文字符");
        }
        $request = array(
            'account' => $account ,
            'password' => $password,
            'phone'=>$phone,
            'name' => $name
        );
        $Business = Common::ImportBusiness("Agent" ,"Admin" );
        $data = $Business->addAgent($request );
        $this->writeActionLog("添加代理商" , "添加代理商" );
        Common::EchoResult($data['code'] , $data['msg']);
    }
    public function editAction(){
        if($this->getRequest()->isPost()){
            $this->doEditAgent();
            exit;
        }
        $params = $this->getParams();
        $agent_id = isset($params['agent_id']) ? $params['agent_id'] : 0 ;
        $AgentModel = new AgentModel();
        //$BankTypeModel = new BankTypeModel();
        $info  = $AgentModel->getInfo( array('agent_id' => $agent_id ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        //$card = $BankTypeModel->getCard();
        $res = array(
            'info' => $info ,
           // 'card' => $card
        );
        $this->displayTemplate('agent/agent_edit' , $res);
    }
    private function doEditAgent(){
        $params = $this->getParams();
        $agent_id = isset($params['agent_id'])?trim($params['agent_id']):'';
        $isEditPasswd = isset($params['isEditPasswd'])?trim($params['isEditPasswd']):'';
        $password= isset($params['password'])?trim($params['password']):'';
        $phone = isset($params['phone'])?trim($params['phone']):'';
        $status = isset($params['status'])?trim($params['status']):'';
        $name = isset($params['name'])?trim($params['name']):'';
        $max_address_count = isset($params['max_address_count'])?trim($params['max_address_count']): 10 ;

        $min_withdraw_money = isset($params['min_withdraw_money'])?trim($params['min_withdraw_money']):'';
        $fixed_poundage = isset($params['fixed_poundage'])?trim($params['fixed_poundage']):'';

        $StringClass = new StringClass();
        if( empty($name) ){
            Common::EchoResult(-3 , "请输入代理商名称");
        }
        if( empty($phone) ){
            Common::EchoResult(-3 , "请输入手机号码");
        }
        if( !Tools::isMobile($phone)){
            Common::EchoResult(-3 , "请输入正确手机号码");
        }
        $request = array(
            'phone'=>$phone,
            'status' => $status,
            'name' => $name ,
            'min_withdraw_money' => $min_withdraw_money ,
            'fixed_poundage' => $fixed_poundage ,
            'max_address_count' => $max_address_count
        );
        if( $isEditPasswd == 1 ){
            if( empty($password) ){
                Common::EchoResult(-3 , "请输入密码");
            }
            if($StringClass->utf8_str($password) != 1 ){
                Common::EchoResult(-3 , "密码必须是英文字符");
            }
            $passwordstrLen = $StringClass->abslength($password) ;
            if($passwordstrLen < 6 or $passwordstrLen > 16 ){
                Common::EchoResult(-3 , "密码必须是6-16位英文字符");
            }
            $request['password'] = $password;
        }
        $Business = Common::ImportBusiness("Agent" ,"Admin" );
        $data = $Business->editAgent($request , $agent_id  );
        $this->writeActionLog("修改代理商" , "修改代理商的基本信息" );
        Common::EchoResult($data['code'] , $data['msg']);
    }
    public function settingFeeAction(){
        if($this->getRequest()->isPost()){
            $this->doSettingFee();
            exit;
        }
        $params = $this->getParams();
        $agent_id =  isset($params['agent_id']) ? intval($params['agent_id']) : 0  ;
        $cid =  isset($params['cid']) ? intval($params['cid']) : 0  ;
        $channel_id =  isset($params['channel_id']) ? intval($params['channel_id']) : 0  ;
        $AgentFeeModel = new AgentFeeModel();
        $feeData = $AgentFeeModel->getInfo( array('id' => $cid ) );
        /*if( !empty($feeData['fee'])){
            $feeData['fee'] = $feeData['fee'] * 100 ;
        }*/
        $res = array(
            'agent_id' => $agent_id ,
            'cid' => $cid ,
            'feeData' => $feeData,
            'channel_id'=>$channel_id
        );
        $this->displayTemplate('agent/setting_fee' , $res);
    }

    private function doSettingFee(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("Agent" ,"Admin" );
        try{
            $res  = $Business->settingFee( $params);
            $this->writeActionLog("设置费率" , "设置代理商费率" );
            Common::EchoResult(1 , "设置成功" );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }


    public function statisticsAction(){
        $params = $this->getParams();
        $agent_id = isset($params['agent_id']) ? $params['agent_id'] : 0 ;
        $action = isset($params['action']) ? trim($params['action']) : "" ;
        if($action == "order_number"){
            $this->order_number();
            exit;
        }if($action == "order_money"){
            $this->order_money();
            exit;
        }
        $t_days = 30 ;
        $AgentModel = new AgentModel();
        $info  = $AgentModel->getInfo( array('agent_id' => $agent_id ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $begin_date = date("Y-m-d" , strtotime("-7 days"));
        $end_date = date("Y-m-d");
        $res = array(
            'info' => $info ,
            'pageSize'=> 10,
            't_days'=> $t_days,
            'begin_date' => $begin_date ,
            'end_date' => $end_date
        );
        $this->displayTemplate('agent/statistics' , $res);
    }
    private function order_number(){
        $params = $this->getParams();
        $agent_id = isset($params['agent_id']) ? $params['agent_id'] : 0 ;
        $begin_date = isset($params['begin_date']) ? $params['begin_date'] : "" ;
        $end_date = isset($params['end_date']) ? $params['end_date'] : ""  ;
        $diffDays = Tools::diffBetweenTwoDays($begin_date,$end_date) ;
        if($diffDays > 30 ){
            $this->echoListJson( 100  ,"日期不能超过30天" );
        }
        $Business = Common::ImportBusiness("Agent" ,"Common" );
        $list = $Business->order_number( $agent_id ,  $begin_date , $end_date );
        $this->echoListJson(0,"OK" , count($list)   ,$list );
    }

    private function order_money(){
        $params = $this->getParams();
        $agent_id = isset($params['agent_id']) ? $params['agent_id'] : 0 ;
        $begin_date = isset($params['begin_date']) ? $params['begin_date'] : "" ;
        $end_date = isset($params['end_date']) ? $params['end_date'] : ""  ;
        $diffDays = Tools::diffBetweenTwoDays($begin_date,$end_date) ;
        if($diffDays > 30 ){
            $this->echoListJson( 100  ,"日期不能超过30天" );
        }
        $Business = Common::ImportBusiness("Agent" ,"Common" );
        $list = $Business->order_money( $agent_id ,  $begin_date , $end_date );
        $this->echoListJson(0,"OK" , count($list)   ,$list );
    }


}