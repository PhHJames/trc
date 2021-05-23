<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-03-26 14:57:33
 */
class Collect_CollectshopController extends Collect_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxList();
            exit;
        }
        /*$Business = Common::ImportBusiness("Order" ,"Merch" );
        $tData = $Business->orderTotal($this->userInfo['user_id']);*/
        $res = array(
            'pageSize'=> 10 ,
            'statusData' => Enum::$orderStatus
        );
        $this->displayTemplate('shop/index' , $res);
    }
    private function getAjaxList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("Shop" ,"Collect" );
        $params['user_id'] = $this->userInfo['user_id'];
        $data = $Business->getCollectShopList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            global $_G;
            $QrStatus = Enum::$QrStatus ;
            foreach ($list as $key => $value) {
                /*$list[$key]['c_money'] ="￥". $value['c_money']/100 ;
                $statusData = Enum::getVal($value['status'] ,$QrStatus )  ;
                $list[$key]['status_string'] =  isset($statusData['value']) ? $statusData['value'] : ""  ;
                $list[$key]['path_pic'] = $_G['config']['domain']['domain']['UPLOADS_HOST'] . $value['path'];*/
                $list[$key]['day_limit_money'] = "￥".$value['day_limit_money'] / 100 ;
                $list[$key]['day_success_money'] = "￥".$value['day_success_money'] / 100 ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }

    public function auto_matchAction(){
        $params = $this->getParams();
        $params['user_id'] = $this->userInfo['user_id'];
        $Business = Common::ImportBusiness("Shop" ,"Collect" );
        try{
            $data = $Business->auto_match( $params );
            Common::EchoResult(1 ,  "操作成功" );
        }catch(Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage());
        }
    }

}