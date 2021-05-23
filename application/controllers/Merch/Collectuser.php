<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Merch_CollectuserController extends Merch_CenterController {
    public function init(){
        parent::init();
        exit("不开放");
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxList();
            exit;
        }
        $res = array(
            'pageSize'=> 10
        );

        $this->displayTemplate('collect_user/user_index' , $res);
    }
    private function getAjaxList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("CollectUser" ,"Merch" );
        $params['mid'] = $this->userInfo['mid'];
        $data = $Business->getCollectUserList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $Api_Business = Common::ImportBusiness("Order" ,"Common" );
            foreach ($list as $key => $value) {
                $list[$key]['day_limit_money'] = "￥" . $value['day_limit_money'] / 100 ;
                $where = " AND user_id = '{$value['user_id']}'";
                $TodayOrderMoney = $Api_Business->getTodayOrderMoney($where);//今日订单金额
                $yestdayOrderMoney = $Api_Business->getYestadyOrderMoney($where);//昨日订单金额
                $list[$key]['TodayOrderMoney'] = $TodayOrderMoney;
                $list[$key]['yestdayOrderMoney'] = $yestdayOrderMoney;
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
        $this->displayTemplate('collect_user/merch_add' , $res);
    }
    private function doAddMerch(){
        $params = $this->getParams();
        $params['mid'] = $this->userInfo['mid'];
        $Business = Common::ImportBusiness("CollectUser" ,"Merch" );
        try{
            $data = $Business->addCollstUser( $params );
            Common::EchoResult(1 ,  "添加成功" );
        }catch(Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage());
        }
    }
    public function editAction(){
        if($this->getRequest()->isPost()){
            $this->doEditCollectUser();
            exit;
        }
        $params = $this->getParams();
        $user_id = isset($params['user_id']) ? $params['user_id'] : 0 ;
        $CollectUserModel = new CollectUserModel();
        //$BankTypeModel = new BankTypeModel();
        $info  = $CollectUserModel->getInfo( array('user_id' => $user_id , 'mid' => $this->userInfo['mid']  ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        //$card = $BankTypeModel->getCard();
        $res = array(
            'info' => $info ,
        );
        $this->displayTemplate('collect_user/merch_edit' , $res);
    }
    private function doEditCollectUser(){
        $params = $this->getParams();
        $user_id = isset($params['user_id'])?trim($params['user_id']):'';
        $isEditPasswd = isset($params['isEditPasswd'])?trim($params['isEditPasswd']):'';
        $password= isset($params['password'])?trim($params['password']):'';
        $phone = isset($params['phone'])?trim($params['phone']):'';
        $status = isset($params['status'])?trim($params['status']):'';
        $name = isset($params['name'])?trim($params['name']):'';
        $day_limit_money = isset($params['day_limit_money'])?trim($params['day_limit_money']):'';
        if( empty($name) ){
            Common::EchoResult(-3 , "请输入码商名称");
        }
        /*if( empty($phone) ){
            Common::EchoResult(-3 , "请输入手机号码");
        }
        if( !Tools::isMobile($phone)){
            Common::EchoResult(-3 , "请输入正确手机号码");
        }*/

        $CollectUserModel = new CollectUserModel();
        //$BankTypeModel = new BankTypeModel();
        $info  = $CollectUserModel->getInfo( array('user_id' => $user_id , 'mid' => $this->userInfo['mid']  ) , true  );
        if( empty($info )){
            Common::EchoResult(-3 , "暂无数据");
        }
        $day_limit_money = $day_limit_money * 100 ;
        if($day_limit_money < $info['day_success_money'] ){
            $s = $info['day_success_money'] / 100 ;
            Common::EchoResult(-3 , "限额金额不能小于：{$s}");
        }
        $StringClass = new StringClass();
        $request = array(
            //'phone'=>$phone,
            'status' => $status,
            'name' => $name ,
            'day_limit_money' => $day_limit_money
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
        $Business = Common::ImportBusiness("CollectUser" ,"Merch" );
        $data = $Business->editCollectUser($request , $user_id  );
        Common::EchoResult($data['code'] , $data['msg']);
    }


    public function auto_matchAction(){
        $params = $this->getParams();
        $params['mid'] = $this->userInfo['mid'];
        $Business = Common::ImportBusiness("CollectUser" ,"Merch" );
        try{
            $data = $Business->auto_match( $params );
            Common::EchoResult(1 ,  "操作成功" );
        }catch(Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage());
        }
    }


}