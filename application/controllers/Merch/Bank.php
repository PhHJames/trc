<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-03-26 14:57:33
 */
class Merch_BankController extends Merch_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        $Business = Common::ImportBusiness("Bank" ,"Merch" );
        $list = $Business->getMerchBankList($this->userInfo['mid']);
        if( !empty($list) ){
            $merchBankStatus = Enum::$merchBankStatus;
            foreach ($list as $key => $val ){
                $statusData = Enum::getVal( $val['status'] ,$merchBankStatus ) ;
                $list[$key]['statusString'] = isset( $statusData['value']) ?  $statusData['value'] : "" ;
            }
        }
        $MerchantsBankModel = new MerchantsBankModel();
        $isAllowAdd = $MerchantsBankModel->isAllowAddCard( $this->userInfo['mid'] );
        $res = array(
            'list' => $list ,
            'isAllowAdd' => $isAllowAdd
        );
        $this->displayTemplate('bank/bank_index' , $res);
    }
    public function addAction(){
        if($this->getRequest()->isPost()){
            $this->doAddCard();
            exit;
        }
        $ChannelModel = new ChannelModel();
        $allChannel = $ChannelModel->getChannel( " AND status = 1 ");
        $res = array(
            'allChannel' => $allChannel ,
        );
        $this->displayTemplate('bank/bank_add' , $res);
    }
    private function doAddCard(){
        $params = $this->getParams();
        try{
            $Business = Common::ImportBusiness("Bank" ,"Merch" );
            $Business->addCard($params , $this->userInfo['mid']);
            Common::EchoResult(1 ,"添加成功,等待审核");
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() ,$e->getMessage());
        }
    }
}