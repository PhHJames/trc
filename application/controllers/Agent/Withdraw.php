<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Agent_WithdrawController extends Agent_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getList();
            exit;
        }
        $AgentModel = new AgentModel();
        $agentInfo = $AgentModel->getInfo( array('agent_id' => $this->agent_id  )  );
        $res = array(
            'pageSize'=> 10 ,
            'statusData' => Enum::$agentWithDrawStatus,
            'agentInfo'=>$agentInfo
        );

        $this->displayTemplate('withdraw/list' , $res);
    }
    private function getList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("Withdraw" ,"Agent" );
        $params['agent_id'] =  $this->agent_id ;
        $data = $Business->getList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $agentWithDrawStatus = Enum::$agentWithDrawStatus;
            foreach ($list as $key => $value) {
                //$list[$key]['money'] = "￥" .   $value['money'] / 100 ;
                //$list[$key]['fixed_poundage'] = "￥" .   $value['fixed_poundage'] / 100 ;
                $agentWithDrawStatusData = Enum::getVal($value['status'] , $agentWithDrawStatus);
                $list[$key]['status'] =  isset($agentWithDrawStatusData['value'])?$agentWithDrawStatusData['value']:"" ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
    public function withdrawAction(){
        if($this->getRequest()->isPost()){
            $this->doWithdraw();
            exit;
        }
        $AgentModel = new AgentModel();
        $Business = Common::ImportBusiness("Withdraw" ,"Agent" );
        $bank =  $Business->getAgentWithdrawCard($this->agent_id);
        $info = $AgentModel->getInfo( array('agent_id' =>  $this->agent_id  ) );
        //$info['fixed_poundage'] = $info['fixed_poundage'] / 100 ;
        $res = array(
            'bank' => $bank,
            'min_withdraw_money'=>$info['min_withdraw_money'],
            'info' => $info
        );
        $this->displayTemplate('withdraw/withdraw' , $res);
    }

    private function doWithdraw(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("Withdraw" ,"Agent" );
        try{
            $res  = $Business->doWithdraw( $params , $this->agent_id );
            Common::EchoResult(1 , "你的提现已申请 请耐心等待...." );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }
}