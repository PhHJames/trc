<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_MerchantController extends Admin_BaseAuthController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxMerchList();
            exit;
        }
        $res = array(
            'pageSize'=> 10
        );

        $this->displayTemplate('merchant/user_index' , $res);
    }
    private function getAjaxMerchList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("Merch" ,"Admin" );
        $data = $Business->getMerchList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            foreach ($list as $key => $value) {
                //$list[$key]['money'] = $value['money']  ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
    //查看商户
    public function  previewAction(){
        $params = $this->getParams();
        $mid =  isset($params['mid']) ? intval($params['mid']) : 0  ;
        $MerchantModel = new MerchantModel();
        $info  = $MerchantModel->getInfo( array('mid' => $mid ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $str = explode("," , $info['white_list_ip'] );
        $white_list_ip = implode("<br>" , $str);
        $info['white_list_ip'] = $white_list_ip ;
        $MerchChannelBusiness = Common::ImportBusiness("Channel" ,"Merch" );
        $channel = $MerchChannelBusiness->getChannelList( array('mid' =>$mid ) );
        if( $channel ){
            foreach ($channel as $key => $val ){
                $channel[$key]['is_open'] = 1 ;
                if( empty($val['merch_channel'])){
                    $channel[$key]['is_open'] =  0  ;
                }else{
                    /*$fee = $val['merch_channel']['fee'] * 10000;

                    $fee = Common::foramtNumber( $fee / 100  , 4 );*/
                    $channel[$key]['merch_channel']['fee'] = $val['merch_channel']['fee'];
                }
            }
        }
        /*echo "<pre>";
        print_r($channel);*/
        $res = array(
            'info' => $info,
            'channel' => $channel
        );
        $this->displayTemplate('merchant/preview' , $res);
    }
    //资金操作
    public function  moneyAction(){
        $params = $this->getParams();
        if($this->getRequest()->isPost()){
            $this->doMoney();
            exit;
        }
        $mid =  isset($params['mid']) ? intval($params['mid']) : 0  ;
        $MerchantModel = new MerchantModel();
        $info  = $MerchantModel->getInfo( array('mid' => $mid ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $res = array(
            'info' => $info,
        );
        $this->displayTemplate('merchant/money' , $res);
    }

    private function doMoney(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("Merch" ,"Admin" );
        try{
            $res  = $Business->doMoney( $params);
            $this->writeActionLog("操作商户的金额" , "操作商户的金额" );
            Common::EchoResult(1 , "操作成功" );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }
    public function addAction(){
        if($this->getRequest()->isPost()){
            $this->doAddMerch();
            exit;
        }
        $this->showError("系统温馨提示" , "暂不支持总后台添加对接商户，请在代理后台添加");
        $res = array(

        );
        $this->displayTemplate('merchant/merch_add' , $res);
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
            'name' => $name
        );
        $Business = Common::ImportBusiness("Merch" ,"Admin" );
        $data = $Business->addMerch($request );
        $this->writeActionLog("添加商户" , "添加商户" );
        Common::EchoResult($data['code'] , $data['msg']);
    }
    public function editAction(){
        if($this->getRequest()->isPost()){
            $this->doEditMerch();
            exit;
        }
        $params = $this->getParams();
        $mid = isset($params['mid']) ? $params['mid'] : 0 ;
        $MerchantModel = new MerchantModel();
        //$BankTypeModel = new BankTypeModel();
        $info  = $MerchantModel->getInfo( array('mid' => $mid ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $str = explode("," , $info['white_list_ip'] );
        $white_list_ip = implode("\n" , $str);
        $info['white_list_ip'] = $white_list_ip ;
        //$card = $BankTypeModel->getCard();
        $res = array(
            'info' => $info ,
            //'card' => $card
        );
        $this->displayTemplate('merchant/merch_edit' , $res);
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
        $min_withdraw_money = isset($params['min_withdraw_money'])?trim($params['min_withdraw_money']):'';
        $fixed_poundage = isset($params['fixed_poundage'])?trim($params['fixed_poundage']):'';
        $num_place_order = isset($params['num_place_order'])?intval($params['num_place_order']): 0 ;

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
            'bank_type'=> intval($bank_type) ,
            'bank_card' =>$bank_card ,
            'notify_url'=>$notify_url,
            'white_list_ip'=>$white_list_ip,
            'status' => $status,
            'name' => $name ,
            'supplement_url'=>$supplement_url ,
            'min_withdraw_money' => $min_withdraw_money ,
            'fixed_poundage' => $fixed_poundage ,
            'num_place_order' => $num_place_order
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
        $Business = Common::ImportBusiness("Merch" ,"Admin" );
        $data = $Business->editMerch($request , $mid  );
        $this->writeActionLog("修改商户" , "修改商户的基本信息" );
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
        $MerchantFeeModel = new MerchantFeeModel();
        $feeData = $MerchantFeeModel->getInfo( array('id' => $cid ) );
        if( !empty($feeData['fee'])){
            //$feeData['fee'] = $feeData['fee'] * 100 ;
        }
        $res = array(
            'mid' => $mid ,
            'cid' => $cid ,
            'feeData' => $feeData,
            'channel_id'=>$channel_id
        );
        $this->displayTemplate('merchant/setting_fee' , $res);
    }

    private function doSettingFee(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("Merch" ,"Admin" );
        try{
            $res  = $Business->settingFee( $params);
            $this->writeActionLog("设置费率" , "设置商户费率" );
            Common::EchoResult(1 , "设置成功" );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }

    /*public function bindcollectuserAction (){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxBindcollectuserList();
            exit;
        }
        $params = $this->getParams();
        $mid =  isset($params['mid']) ? intval($params['mid']) : 0  ;
        $MerchantModel = new MerchantModel();
        $info = $MerchantModel->getInfo( array('mid' => $mid ) );
        $res = array(
            'mid' => $mid ,
            'info' => $info,
            'pageSize'=> 100
        );
        $this->displayTemplate('merchant/bindcollectuser' , $res);
    }*/

    /*private function getAjaxBindcollectuserList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $pageSize = 10000 ;
        $Business = Common::ImportBusiness("Merch" ,"Admin" );
        $data = $Business->getAjaxBindcollectuserList($params,$pageSize);
        $list = $data['list'];
        $MerchantsBindCollectModel = new MerchantsBindCollectModel();
        $mid =  isset($params['mid']) ? intval($params['mid']) : 0  ;
        $uidData = $MerchantsBindCollectModel->getMerchCollect($mid);//当前商户绑定的
        if( !empty($list) ){
            foreach ($list as $key => $value) {
                $userBind = $MerchantsBindCollectModel->getUserBindMerch($value['user_id']);
                if(!empty($userBind)){
                    if( $userBind['mid'] != $mid ){
                        unset($list[$key]);
                        continue;
                    }
                }
                $bind =  0  ;
                $bindStr = "未绑定";
                if( in_array($value['user_id']  ,$uidData )){
                    $bind = 1 ;
                    $bindStr = "已绑定";
                }
                $list[$key]['bind'] = $bind ;
                $list[$key]['bindStr'] = $bindStr ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }*/
    /*public function doBindCollectUserAction(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("Merch" ,"Admin" );
        try{
            $res  = $Business->doBindCollectUser( $params);
            $this->writeActionLog("商户绑定码商" , "商户绑定码商" );
            Common::EchoResult(1 , "设置成功" );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }
    public function doDeleteBindCollectUserAction(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("Merch" ,"Admin" );
        try{
            $res  = $Business->doDeleteBindCollectUser( $params);
            $this->writeActionLog("商户删除绑定码商" , "商户删除绑定码商" );
            Common::EchoResult(1 , "设置成功" );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }*/


    public function statisAction(){
        $params = $this->getParams();
        $begin_date = isset($params['begin_date']) ? $params['begin_date'] :  '';
        $end_date = isset($params['end_date']) ? $params['end_date'] :  '';
        $mid = isset($params['mid']) ? $params['mid'] :   0 ;
        $begin_date = $begin_date ? $begin_date :  date("Y-m-d" , strtotime("-7 days"));
        $end_date = $end_date ? $end_date :  date("Y-m-d");
        $days = Tools::diffBetweenTwoDays($begin_date , $end_date );
        if($days > 30 or $days <= 0  ){
            $this->showError("温馨提示" , "时间范围必须大于1小于30");
        }
        $commonOrderBusiness = Common::ImportBusiness("Order" , "Common");
        $list = $commonOrderBusiness->getStatisByDate( $begin_date , $end_date , " AND mid = '{$mid}' " );
        $res = array(
            'begin_date' => $begin_date,
            'end_date' => $end_date,
            'list' => $list,
            'mid' => $mid
        );
        $this->displayTemplate('merchant/statis' , $res);
    }


}