<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_AddresstranslogController  extends Admin_BaseAuthController {
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
        );

        $this->displayTemplate('address_trans_log/list' , $res);
    }
    private function getList(){
        /*    $LogModel = new LogModel();
            echo  $l = $LogModel->updateData( ['type' => "test..."] , ['id'=>203 ]   );*/
        //print_r($l);
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("AddressTransLog" ,"Admin" );
        $data = $Business->getList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            foreach ($list as $key => $value) {
                //$list[$key]['message'] =  htmlspecialchars_decode($value['extra']) ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
}