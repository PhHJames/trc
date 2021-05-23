<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Merch_WithdrawController extends Merch_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getList();
            exit;
        }
        $MerchantModel = new MerchantModel();
        $merchInfo = $MerchantModel->getInfo( array('mid' => $this->userInfo['mid']  )  );
        $res = array(
            'pageSize'=> 10 ,
            'statusData' => Enum::$merchWithDrawStatus ,
            'merchInfo'=>$merchInfo
        );

        $this->displayTemplate('withdraw/list' , $res);
    }
    private function getList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("Withdraw" ,"Merch" );
        $params['mid'] =  $this->userInfo['mid'] ;
        $data = $Business->getList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $merchWithDrawStatus = Enum::$merchWithDrawStatus;
            foreach ($list as $key => $value) {
                $merchWithDrawStatusData = Enum::getVal($value['status'] , $merchWithDrawStatus);
                $list[$key]['status'] =  isset($merchWithDrawStatusData['value'])?$merchWithDrawStatusData['value']:"" ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
    public function withdrawAction(){
        if($this->getRequest()->isPost()){
            $this->doWithdraw();
            exit;
        }
        $MerchantModel = new MerchantModel();
        $mid = $this->userInfo['mid'] ;
        $Business = Common::ImportBusiness("Withdraw" ,"Merch" );
        $bank =  $Business->getMerchWithdrawCard($mid);
        $info = $MerchantModel->getInfo( array('mid' => $mid ) );
        $min_withdraw_money = $info['min_withdraw_money'];
        $info['fixed_poundage'] = $info['fixed_poundage'] ;
        $res = array(
            'bank' => $bank,
            'min_withdraw_money'=>$min_withdraw_money,
            'info' => $info
        );
        $this->displayTemplate('withdraw/withdraw' , $res);
    }

    private function doWithdraw(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("Withdraw" ,"Merch" );
        try{
            $res  = $Business->doWithdraw( $params , $this->userInfo['mid']);
            Common::EchoResult(1 , "你的提现已申请 请耐心等待...." );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }
}