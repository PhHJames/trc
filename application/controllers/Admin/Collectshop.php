<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_CollectshopController extends Admin_BaseAuthController {
    public function init(){
        parent::init();
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

        $this->displayTemplate('collect_shop/index' , $res);
    }
    private function getAjaxList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("CollectShop" ,"Admin" );
        $data = $Business->getCollectShopList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            foreach ($list as $key => $value) {
                $list[$key]['day_limit_money'] = "￥".$value['day_limit_money'] / 100 ;
                $list[$key]['day_success_money'] = "￥".$value['day_success_money'] / 100 ;
                $list[$key]['all_success'] = 0;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
    public function editAction(){
        if($this->getRequest()->isPost()){
            $this->doEditShop();
            exit;
        }
        $params = $this->getParams();
        $shop_id = isset($params['shop_id']) ? $params['shop_id'] : 0 ;
        $CollectShopModel = new CollectShopModel();
        //$BankTypeModel = new BankTypeModel();
        $info  = $CollectShopModel->getInfo( array('shop_id' => $shop_id ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        //$card = $BankTypeModel->getCard();
        $res = array(
            'info' => $info ,
        );
        $this->displayTemplate('collect_shop/edit' , $res);
    }
    private function doEditShop(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("CollectShop" ,"Admin" );
        try{
            $res  = $Business->do_edit_shop( $params);
            $this->writeActionLog("修改店铺信息" , "修改店铺信息" );
            Common::EchoResult(1 , "设置成功" );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }


        $params = $this->getParams();
        $user_id = isset($params['user_id'])?trim($params['user_id']):'';
        $isEditPasswd = isset($params['isEditPasswd'])?trim($params['isEditPasswd']):'';
        $password= isset($params['password'])?trim($params['password']):'';
        $phone = isset($params['phone'])?trim($params['phone']):'';
        $status = isset($params['status'])?trim($params['status']):'';
        $name = isset($params['name'])?trim($params['name']):'';
        if( empty($name) ){
            Common::EchoResult(-3 , "请输入码商名称");
        }
        if( empty($phone) ){
            Common::EchoResult(-3 , "请输入手机号码");
        }
        if( !Tools::isMobile($phone)){
            Common::EchoResult(-3 , "请输入正确手机号码");
        }
        $StringClass = new StringClass();
        $request = array(
            'phone'=>$phone,
            'status' => $status,
            'name' => $name ,
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
        $Business = Common::ImportBusiness("CollectUser" ,"Admin" );
        $data = $Business->editCollectUser($request , $user_id  );
        $this->writeActionLog("修改码商" , "修改码商的基本信息" );
        Common::EchoResult($data['code'] , $data['msg']);
    }

}