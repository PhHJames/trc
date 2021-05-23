<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Agent_MerchController extends Agent_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getList();
            exit;
        }
        $res = array(
            'pageSize'=> 50 ,
            'typeData' => Enum::$merchMoneyLogType ,
        );

        $this->displayTemplate('merch/list' , $res);
    }
    private function getList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("Merch" ,"Agent" );
        $params['agent_id'] =  $this->userInfo['agent_id'] ;
        $data = $Business->getMerchList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $merchMoneyLogType = Enum::$merchMoneyLogType;
            foreach ($list as $key => $value) {
                //$list[$key]['money'] = "￥" .  $value['money'] / 100 ;
                /*$list[$key]['account_money'] = "￥" .  $value['account_money'] / 100 ;
                $list[$key]['now_money'] = "￥" .  $value['now_money'] / 100 ;
                $merchMoneyTypeData = Enum::getVal($value['type'] , $merchMoneyLogType);
                $list[$key]['typeString'] =  isset($merchMoneyTypeData['value'])?$merchMoneyTypeData['value']:$merchMoneyTypeData['value'] ;*/
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }

    public function addAction(){
        if($this->getRequest()->isPost()){
            $this->doAddMerch();
            exit;
        }
        $res = array(

        );
        $this->displayTemplate('merch/merch_add' , $res);
    }
    private function doAddMerch(){
        $params = $this->getParams();
        $account = isset($params['account'])?trim($params['account']):'';
        $password= isset($params['password'])?trim($params['password']):'';
        $phone = isset($params['phone'])?trim($params['phone']):'';
        $name = isset($params['name'])?trim($params['name']):'';
        if( empty($name) ){
            Common::EchoResult(-3 , "请输入商户名称");
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
            'name' => $name ,
            'agent_id' => $this->userInfo['agent_id'],
            'status' => 0
        );
        $Business = Common::ImportBusiness("Merch" ,"Agent" );
        $data = $Business->addMerch($request );
        Common::EchoResult($data['code'] , $data['msg']);
    }

    public function editAction(){
        if($this->getRequest()->isPost()){
            $this->doEditMerch();
            exit;
        }
        $params = $this->getParams();
        $mid = isset($params['mid']) ? $params['mid'] : 0 ;
        $agent_id = $this->userInfo['agent_id'];
        $MerchantModel = new MerchantModel();
        $info  = $MerchantModel->getInfo( array('mid' => $mid , 'agent_id' => $agent_id  ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $str = explode("," , $info['white_list_ip'] );
        $white_list_ip = implode("\n" , $str);
        $info['white_list_ip'] = $white_list_ip ;
        $res = array(
            'info' => $info ,
        );
        $this->displayTemplate('merch/merch_edit' , $res);
    }
    private function doEditMerch(){
        $params = $this->getParams();
        $mid = isset($params['mid'])?trim($params['mid']):'';
        $isEditPasswd = isset($params['isEditPasswd'])?trim($params['isEditPasswd']):'';
        $password= isset($params['password'])?trim($params['password']):'';
        $phone = isset($params['phone'])?trim($params['phone']):'';
        $bank_type = isset($params['bank_type'])?trim($params['bank_type']):'';
        $bank_card = isset($params['bank_card'])?trim($params['bank_card']):'';
        $white_list_ip = isset($params['white_list_ip'])?trim($params['white_list_ip']):'';
        $notify_url = isset($_REQUEST['notify_url'])?trim($_REQUEST['notify_url']):"";
        $supplement_url = isset($_REQUEST['supplement_url'])?trim($_REQUEST['supplement_url']):"";
        $status = isset($params['status'])?trim($params['status']):'';
        $name = isset($params['name'])?trim($params['name']):'';

        $StringClass = new StringClass();
        if($white_list_ip){
            $d = explode("\n" , $white_list_ip) ;
            $white_list_ip = implode("," , $d );
        }
        if( empty($name) ){
            Common::EchoResult(-3 , "请输入商户名称");
        }
        if( empty($phone) ){
            Common::EchoResult(-3 , "请输入手机号码");
        }
        if( !Tools::isMobile($phone)){
            Common::EchoResult(-3 , "请输入正确手机号码");
        }
        $request = array(
            'phone'=>$phone,
            //'bank_type'=> intval($bank_type) ,
            //'bank_card' =>$bank_card ,
            //'notify_url'=>$notify_url,
            //'white_list_ip'=>$white_list_ip,
           'status' => $status,
            'name' => $name ,
            //'supplement_url'=>$supplement_url
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
        $Business = Common::ImportBusiness("Merch" ,"Agent" );
        $data = $Business->editMerch($request , $mid  );
        Common::EchoResult($data['code'] , $data['msg']);
    }

    public function settingFeeAction(){
        if($this->getRequest()->isPost()){
            $this->doSettingFee();
            exit;
        }
        $params = $this->getParams();
        $mid =  isset($params['mid']) ? intval($params['mid']) : 0  ;
        $cid =  isset($params['cid']) ? intval($params['cid']) : 0  ;
        $channel_id =  isset($params['channel_id']) ? intval($params['channel_id']) : 0  ;
        $agent_channelid =  isset($params['agent_channelid']) ? intval($params['agent_channelid']) : 0  ;
        $MerchantFeeModel = new MerchantFeeModel();
        $feeData = $MerchantFeeModel->getInfo( array('id' => $cid ) );
        /*if( !empty($feeData['fee'])){
            $feeData['fee'] = $feeData['fee'] * 100 ;
        }*/
        $res = array(
            'mid' => $mid ,
            'cid' => $cid ,
            'feeData' => $feeData,
            'channel_id'=>$channel_id,
            'agent_channelid'=>$agent_channelid
        );
        $this->displayTemplate('merch/setting_fee' , $res);
    }

    private function doSettingFee(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("Merch" ,"Agent" );
        try{
            $res  = $Business->settingFee( $params ,$this->userInfo['agent_id'] );
            Common::EchoResult(1 , "设置成功" );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }

    public function  previewAction(){
        $params = $this->getParams();
        $mid =  isset($params['mid']) ? intval($params['mid']) : 0  ;
        $MerchantModel = new MerchantModel();
        $info  = $MerchantModel->getInfo( array('mid' => $mid , 'agent_id' => $this->userInfo['agent_id']   ) );
        if( empty( $info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $str = explode("," , $info['white_list_ip'] );
        $white_list_ip = implode("<br>" , $str);
        $info['white_list_ip'] = $white_list_ip ;
        $AgentChannelBusiness = Common::ImportBusiness("Channel" ,"Agent" );
        $channel = $AgentChannelBusiness->getChannelList( array('mid' =>$mid ) ,$this->userInfo['agent_id']  , $mid );
        if( $channel ){
            foreach ($channel as $key => $val ){
                $channel[$key]['is_open'] = 1 ;
                if( empty($val['merch_channel'])){
                    $channel[$key]['is_open'] =  0  ;
                }else{
                   // $fee = $val['merch_channel']['fee'] * 100 ;
                   // $fee = Common::foramtNumber( $fee , 2 );
                    //$channel[$key]['merch_channel']['fee'] = $fee;
                }
            }
        }
        /*echo "<pre>";
        print_r($channel);*/
        $res = array(
            'info' => $info,
            'channel' => $channel
        );
        $this->displayTemplate('merch/preview' , $res);
    }
    public function statisticsAction(){
        $params = $this->getParams();
        $mid  = isset($params['mid']) ? $params['mid'] : 0 ;
        $action = isset($params['action']) ? trim($params['action']) : "" ;
        if($action == "order_number"){
            $this->order_number();
            exit;
        }if($action == "order_money"){
            $this->order_money();
            exit;
        }
        $MerchantModel = new MerchantModel();
        $info  = $MerchantModel->getInfo( array('mid' => $mid ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $begin_date = date("Y-m-d" , strtotime("-7 days"));
        $end_date = date("Y-m-d");
        $res = array(
            'info' => $info ,
            'pageSize'=> 10,
            'begin_date' => $begin_date ,
            'end_date' => $end_date
        );
        $this->displayTemplate('merch/statistics' , $res);
    }
    private function order_number(){
        $params = $this->getParams();
        $mid = isset($params['mid']) ? $params['mid'] : 0 ;
        $begin_date = isset($params['begin_date']) ? $params['begin_date'] : "" ;
        $end_date = isset($params['end_date']) ? $params['end_date'] : ""  ;
        $diffDays = Tools::diffBetweenTwoDays($begin_date,$end_date) ;
        if($diffDays > 30 ){
            $this->echoListJson( 100  ,"日期不能超过30天" );
        }
        $Business = Common::ImportBusiness("Merch" ,"Common" );
        $list = $Business->order_number( $mid,  $begin_date , $end_date , " AND agent_id = " . $this->agent_id  );
        //$this->echoListJson(1 ,"OK" , count($list)   ,$list );
        $this->echoListJson(0,"OK" , count($list)   ,$list );
    }

    private function order_money(){
        $params = $this->getParams();
        $mid = isset($params['mid']) ? $params['mid'] : 0 ;
        $begin_date = isset($params['begin_date']) ? $params['begin_date'] : "" ;
        $end_date = isset($params['end_date']) ? $params['end_date'] : ""  ;
        $diffDays = Tools::diffBetweenTwoDays($begin_date,$end_date) ;
        if($diffDays > 30 ){
            $this->echoListJson( 100  ,"日期不能超过30天" );
        }
        $Business = Common::ImportBusiness("Merch" ,"Common" );
        $list = $Business->order_money( $mid,  $begin_date , $end_date  , " AND agent_id = " . $this->agent_id , $this->agent_id );
        $this->echoListJson(0,"OK" , count($list)   ,$list );
    }
}