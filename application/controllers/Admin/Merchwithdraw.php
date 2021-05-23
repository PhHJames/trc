<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_MerchwithdrawController extends Admin_BaseAuthController {
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
            'pageSize'=> 10 ,
            'statusData' => Enum::$merchWithDrawStatus ,
        );

        $this->displayTemplate('merchant_draw/list' , $res);
    }
    private function getList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("MerchWithDraw" ,"Admin" );
        $data = $Business->getList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $merchWithDrawStatus = Enum::$merchWithDrawStatus;
            $merchWithComeFrom = Enum::$merchWithComeFrom;

            foreach ($list as $key => $value) {
                $merchWithDrawStatusData = Enum::getVal($value['status'] , $merchWithDrawStatus);
                $list[$key]['statusString'] =  isset($merchWithDrawStatusData['value'])?$merchWithDrawStatusData['value']:"" ;

                $merchWithComeFromData = Enum::getVal($value['come_from'] , $merchWithComeFrom);
                $list[$key]['comeString'] =  isset($merchWithComeFromData['value'])?$merchWithComeFromData['value']:"" ;


                $list[$key]['txid'] = $value['txid']?$value['txid']:"" ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
    public function authAction(){
        $params = $this->getParams();
        $action = isset($params['action']) ? $params['action'] :"";
        if($action == 'show_success' ){
            $this->displayShowAuthSuccess();
            exit;
        }elseif($action == 'trans' ){
            $this->trans_usdt();
            exit;
        }
        try{
            $Business = Common::ImportBusiness("MerchWithDraw" ,"Admin" );
            $Business->auth($_POST);
            $this->writeActionLog("审核商户提现" , "审核商户提现" );
            Common::EchoResult(1, "操作成功");
        }catch(Exception $e ){
            Common::EchoResult($e->getCode(), $e->getMessage());
        }
    }
    //审核通过的页面
    private function displayShowAuthSuccess(){
        $params = $this->getParams();
        $id  = isset($params['id']) ? $params['id'] : 0 ;
        $MerchantWithDrawModel = new MerchantWithDrawModel();
        $info = $MerchantWithDrawModel->getInfo(['id' => $id ]);
        $MerchantsBankModel = new MerchantsBankModel();
        $bank_info = $MerchantsBankModel->getInfo(['id' => $info['bank_id'] ]);
        $Source_Account = new Source_Account();
        $SummaryAccount = $Source_Account->getSummaryAccountList();
        $res= [
            'info' => $info,
            'bank_info' => $bank_info ,
            'SummaryAccount' => $SummaryAccount ,
            'txid' => ($info['txid']) ? $info['txid'] :'',
        ];
        $this->displayTemplate('merchant_draw/displayShowAuthSuccess' , $res);
    }
    private function trans_usdt(){
        $params = $this->getParams();
        try{
            $Business = Common::ImportBusiness("MerchWithDraw" ,"Admin" );
            $Business->trans_usdt($params);
            $this->writeActionLog("给商户打款" , "给商户打款" );
            Common::EchoResult(1, "操作成功");
        }catch(Exception $e ){
            Common::EchoResult($e->getCode(), $e->getMessage());
        }
    }
}