<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-03-26 14:57:33
 */
class Collect_QrimportlogController extends Collect_CenterController {
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
        $this->displayTemplate('qrimport_log/index' , $res);
    }
    private function getAjaxList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("QrImportLog" ,"Collect" );
        $params['user_id'] = $this->userInfo['user_id'];
        $data = $Business->getCollectQrImportLogList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            global $_G;
            $QrStatus = Enum::$ImportQrStatus ;
            foreach ($list as $key => $value) {
                $statusData = Enum::getVal($value['status'] ,$QrStatus )  ;
                $list[$key]['status_string'] =  isset($statusData['value']) ? $statusData['value'] : ""  ;

                /*$list[$key]['c_money'] ="￥". $value['c_money']/100 ;

                $list[$key]['path_pic'] = $_G['config']['domain']['domain']['UPLOADS_HOST'] . $value['path'];*/
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }



}