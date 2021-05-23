<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_AgentbankController extends Admin_BaseAuthController {
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
            'pageSize'=> 10,
            'merchBankStatus' => Enum::$agentBankStatus,
        );

        $this->displayTemplate('agent_bank/bank_index' , $res);
    }
    private function getAjaxList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("AgentBank" ,"Admin" );
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

        $AgentBankModel = new AgentBankModel();
        $info  = $AgentBankModel->getInfo( array('id' => $id ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }

        $res = array(
            'info' => $info,
            'agentBankStatus' => Enum::$agentBankStatus,
        );
        $this->displayTemplate('agent_bank/manager' , $res);
    }
    private function doManager(){
        try{
            $Business = Common::ImportBusiness("AgentBank" ,"Admin" );
            $Business->doManager($_POST);
            $this->writeActionLog("设置代理商提交的币地址" , "设置代理商提交的币地址" );
            Common::EchoResult(1, "操作成功");
        }catch(Exception $e ){
            Common::EchoResult($e->getCode(), $e->getMessage());
        }
    }
}