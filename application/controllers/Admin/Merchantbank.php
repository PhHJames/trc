<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_MerchantbankController extends Admin_BaseAuthController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxList();
            exit;
        }
        $merchBankStatus = Enum::$merchBankStatus;
        $res = array(
            'pageSize'=> 10,
            'merchBankStatus' => Enum::$merchBankStatus,
        );

        $this->displayTemplate('merchant_bank/bank_index' , $res);
    }
    private function getAjaxList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("MerchBank" ,"Admin" );
        $data = $Business->getList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $merchBankStatus = Enum::$merchBankStatus;
            foreach ($list as $key => $val ){
                $statusData = Enum::getVal( $val['status'] ,$merchBankStatus ) ;
                $list[$key]['statusString'] = isset( $statusData['value']) ?  $statusData['value'] : "" ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
    public function  managerAction(){
        $params = $this->getParams();
        if($this->getRequest()->isPost()){
            $this->doManager();
            exit;
        }
        $id =  isset($params['id']) ? intval($params['id']) : 0  ;
        $MerchantsBankModel = new MerchantsBankModel();
        $info  = $MerchantsBankModel->getInfo( array('id' => $id ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $BankTypeModel = new BankTypeModel();
        $res = array(
            'info' => $info,
            'merchBankStatus' => Enum::$merchBankStatus,
        );
        $this->displayTemplate('merchant_bank/manager' , $res);
    }
    private function doManager(){
        try{
            $Business = Common::ImportBusiness("MerchBank" ,"Admin" );
            $Business->doManager($_POST);
            $this->writeActionLog("设置商户提交的币" , "设置商户提交的币" );
            Common::EchoResult(1, "操作成功");
        }catch(Exception $e ){
            Common::EchoResult($e->getCode(), $e->getMessage());
        }
    }
}