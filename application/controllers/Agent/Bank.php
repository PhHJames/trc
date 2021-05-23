<?php
/**
 * @Author: 不要复制我的代码不然后果自负
 * @Date:   2018-10-20 22:30:23
 * @Last Modified by:   Awe
 * @Last Modified time: 2019-03-26 14:57:33
 */
class Agent_BankController extends Agent_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        $Business = Common::ImportBusiness("Bank" ,"Agent" );
        $list = $Business->getAgentBankList($this->userInfo['agent_id']);
        if( !empty($list) ){
            $merchBankStatus = Enum::$agentBankStatus;
            foreach ($list as $key => $val ){
                $statusData = Enum::getVal( $val['status'] ,$merchBankStatus ) ;
                $list[$key]['statusString'] = isset( $statusData['value']) ?  $statusData['value'] : "" ;
            }
        }
        $AgentBankModel = new AgentBankModel();
        $isAllowAdd = $AgentBankModel->isAllowAddCard( $this->userInfo['agent_id'] );
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
            $Business = Common::ImportBusiness("Bank" ,"Agent" );
            $Business->addCard($params , $this->userInfo['agent_id']);
            Common::EchoResult(1 ,"添加成功,等待审核");
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() ,$e->getMessage());
        }
    }

    public function editAction(){
        if($this->getRequest()->isPost()){
            $this->doEditCard();
            exit;
        }
        $params = $this->getParams();
        $id = isset($params['id'])?trim($params['id']):'';
        $BankTypeModel = new BankTypeModel();
        $bank = $BankTypeModel->getCard(1);

        $AgentBankModel = new AgentBankModel();
        $info = $AgentBankModel->getInfo(['id'=>$id]);
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $res = array(
            'bank' => $bank ,
            'info' => $info
        );
        $this->displayTemplate('bank/bank_edit' , $res);
    }
    private function doEditCard(){
        $params = $this->getParams();
        try{
            $Business = Common::ImportBusiness("Bank" ,"Agent" );
            $Business->editCard($params);
            Common::EchoResult(1 ,"编辑成功,等待审核");
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() ,$e->getMessage());
        }
    }
}