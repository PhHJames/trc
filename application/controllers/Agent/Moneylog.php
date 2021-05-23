<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Agent_MoneylogController extends Agent_CenterController {
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
            'pageSize'=> 50 ,
            'typeData' => Enum::$agentMoneyLogType ,
        );

        $this->displayTemplate('money_log/list' , $res);
    }
    private function getList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("MoneyLog" ,"Agent" );
        $params['agent_id'] =  $this->agent_id ;
        $data = $Business->getList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $merchMoneyLogType = Enum::$merchMoneyLogType;
            foreach ($list as $key => $value) {
                //$list[$key]['money'] = "￥" .  $value['money'] / 100 ;
                //$list[$key]['account_money'] = "￥" .  $value['account_money'] / 100 ;
                //$list[$key]['now_money'] = "￥" .  $value['now_money'] / 100 ;
                $merchMoneyTypeData = Enum::getVal($value['type'] , $merchMoneyLogType);
                $list[$key]['typeString'] =  isset($merchMoneyTypeData['value'])?$merchMoneyTypeData['value']:$merchMoneyTypeData['value'] ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
}