<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_LogController extends Admin_BaseAuthController {
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

        $this->displayTemplate('log/list' , $res);
    }
    private function getList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("Log" ,"Admin" );
        $data = $Business->getList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $merchMoneyLogType = Enum::$merchMoneyLogType;
            foreach ($list as $key => $value) {
                //$list[$key]['money'] = "￥" . $value['money'] / 100 ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
}