<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_MerchmoneylogController extends Admin_BaseAuthController {
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
            'typeData' => Enum::$merchMoneyLogType ,
        );

        $this->displayTemplate('merchant_money/list' , $res);
    }
    private function getList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("MerchMoneyLog" ,"Admin" );
        $data = $Business->getList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $merchMoneyLogType = Enum::$merchMoneyLogType;
            foreach ($list as $key => $value) {
                //$list[$key]['money'] = "￥" . $value['money'] / 100 ;
                //$list[$key]['account_money'] = "￥" .$value['account_money'] / 100 ;
                //$list[$key]['now_money'] = $value['now_money'] / 100 ;
                $merchMoneyLogTypeData = Enum::getVal($value['type'] , $merchMoneyLogType);
                $list[$key]['typeString'] =  isset($merchMoneyLogTypeData['value'])?$merchMoneyLogTypeData['value']:"" ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
    public function authAction(){
        try{
            $Business = Common::ImportBusiness("MerchWithDraw" ,"Admin" );
            $Business->auth($_POST);
            $this->writeActionLog("审核商户提现" , "审核商户提现" );
            Common::EchoResult(1, "操作成功");
        }catch(Exception $e ){
            Common::EchoResult($e->getCode(), $e->getMessage());
        }
    }
}