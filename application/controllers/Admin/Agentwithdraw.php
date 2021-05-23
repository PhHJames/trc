<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_AgentwithdrawController extends Admin_BaseAuthController {
    public function init(){
        $this->NotAuthExclusion=["querynotdo"];
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
            'statusData' => Enum::$agentWithDrawStatus ,
        );

        $this->displayTemplate('agent_draw/list' , $res);
    }
    private function getList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("AgentWithDraw" ,"Admin" );
        $data = $Business->getList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $agentWithDrawStatus = Enum::$agentWithDrawStatus;
            foreach ($list as $key => $value) {
                //$list[$key]['money'] = "￥" . $value['money'] / 100 ;
                //$list[$key]['fixed_poundage'] = "￥" .$value['fixed_poundage'] / 100 ;
                $agentWithDrawStatusData = Enum::getVal($value['status'] , $agentWithDrawStatus);
                $list[$key]['statusString'] =  isset($agentWithDrawStatusData['value'])?$agentWithDrawStatusData['value']:"" ;
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
            $Business = Common::ImportBusiness("AgentWithDraw" ,"Admin" );
            $Business->auth($_POST);
            $this->writeActionLog("审核代理商提现" , "审核代理商提现" );
            Common::EchoResult(1, "操作成功");
        }catch(Exception $e ){
            Common::EchoResult($e->getCode(), $e->getMessage());
        }
    }
    //审核通过的页面
    private function displayShowAuthSuccess(){
        $params = $this->getParams();
        $id  = isset($params['id']) ? $params['id'] : 0 ;
        $AgentWithDrawModel = new AgentWithDrawModel();
        $info = $AgentWithDrawModel->getInfo(['id' => $id ]);
        $AgentBankModel = new AgentBankModel();
        $bank_info = $AgentBankModel->getInfo(['id' => $info['bank_id'] ]);
        $Source_Account = new Source_Account();
        $SummaryAccount = $Source_Account->getSummaryAccountList();
        $res= [
            'info' => $info,
            'bank_info' => $bank_info ,
            'SummaryAccount' => $SummaryAccount ,
            'txid' => ($info['txid']) ? $info['txid'] :'',
        ];
        $this->displayTemplate('agent_draw/displayShowAuthSuccess' , $res);
    }
    private function trans_usdt(){
        $params = $this->getParams();
        try{
            $Business = Common::ImportBusiness("AgentWithDraw" ,"Admin" );
            $Business->trans_usdt($params);
            $this->writeActionLog("给代理打款" , "给代理打款" );
            Common::EchoResult(1, "操作成功");
        }catch(Exception $e ){
            Common::EchoResult($e->getCode(), $e->getMessage());
        }
    }
    public function querynotdoAction(){
        $Business = Common::ImportBusiness("AgentWithDraw" ,"Admin" );
        $info = $Business->withdrawNotDo();
        if( $info ) {
            Common::EchoResult(1,"有提现未处理" );
        }
        Common::EchoResult(0,"没查询到数据" );
    }
}