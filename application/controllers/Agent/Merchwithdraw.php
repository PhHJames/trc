<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Agent_MerchwithdrawController extends Agent_CenterController {
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
        $Business = Common::ImportBusiness("MerchWithDraw" ,"Agent" );
        $agent_id =  $this->userInfo['agent_id'] ;
        $data = $Business->getList($params,$pageSize,$agent_id);
        $list = $data['list'];
        if( !empty($list) ){
            $merchWithDrawStatus = Enum::$merchWithDrawStatus;
            foreach ($list as $key => $value) {
                $list[$key]['money'] = "￥" . $value['money'] / 100 ;
                $list[$key]['fixed_poundage'] = "￥" .$value['fixed_poundage'] / 100 ;
                $merchWithDrawStatusData = Enum::getVal($value['status'] , $merchWithDrawStatus);
                $list[$key]['statusString'] =  isset($merchWithDrawStatusData['value'])?$merchWithDrawStatusData['value']:"" ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
}