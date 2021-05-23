<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_SystemaddressController extends Admin_BaseAuthController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxList();
            exit;
        }


        $Source_Account = new Source_Account();
        $SummaryAccount = $Source_Account->getSummaryAccountList();
        $TrxList = $Source_Account->getTrxList();
        //print_r($SummaryAccount);
        $res = array(
            'SummaryAccount'=> $SummaryAccount,
            'TrxList' => $TrxList,
        );
        $this->displayTemplate('system_address/index' , $res);
    }


    public function Transfer_outAction(){
        if($this->getRequest()->isPost()){
            $this->do_transfer_to();
            exit;
        }
        $params = $this->getParams();
        $address = isset($params['address']) ? $params['address'] :"";
        global $_G;
        $res = [
            'address' => $address
        ] ;
        $this->displayTemplate('system_address/Transfer_out' , $res);
    }

    private function do_transfer_to(){
        $params = $_POST;
        $Business = Common::ImportBusiness("SystemAddress" ,"Admin" );
        try{
            $res  = $Business->do_transfer_to( $params);
            $this->writeActionLog("USDT币转出" , "USDT币转出" );
            Common::EchoResult(1 , "操作成功" );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }

    public function Transfer_trx_outAction(){
        if($this->getRequest()->isPost()){
            $this->do_transfer_trx_to();
            exit;
        }
        $params = $this->getParams();
        $address = isset($params['address']) ? $params['address'] :"";
        global $_G;
        $res = [
            'address' => $address
        ] ;
        $this->displayTemplate('system_address/Transfer_out_trx' , $res);
    }

    private function do_transfer_trx_to(){
        $params = $_POST;
        $Business = Common::ImportBusiness("SystemAddress" ,"Admin" );
        try{
            $res  = $Business->do_transfer_trx_to( $params);
            $this->writeActionLog("TRX币转出" , "TRX币转出" );
            Common::EchoResult(1 , "操作成功" );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }


    public function auto_matchAction(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("CollectQrcode" ,"Admin" );
        try{
            $data = $Business->auto_match( $params );
            Common::EchoResult(1 ,  "操作成功" );
        }catch(Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage());
        }
    }

    public function activeAction(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("CollectQrcode" ,"Admin" );
        try{
            $data = $Business->active( $params );
            Common::EchoResult(1 ,  "操作成功" );
        }catch(Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage());
        }
    }

    public function  previewAction(){
        $params = $this->getParams();
        $qr_id =  isset($params['qr_id']) ? trim($params['qr_id']) : ""  ;
        $CollectQrcodeModel = new CollectQrcodeModel();
        $info  = $CollectQrcodeModel->getInfo( array('qr_id' => $qr_id ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        global $_G;
        $info['path_pic'] = $_G['config']['domain']['domain']['UPLOADS_HOST'] . $info['path'];
        $res = array(
            'info' => $info,
        );
        $this->displayTemplate('collect_qrcode/preview' , $res);
    }

    function updatePrivateAction(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("CollectQrcode" ,"Admin" );
        try{
            $data = $Business->updatePrivate( $params );
            $is_first = isset($data['is_first']) ? $data['is_first'] :"";
            $privatekey = '';
            if( $is_first ){
                $privatekey = isset($data['privatekey']) ? $data['privatekey'] :"";
            }
            Common::EchoResult(1 ,  "操作成功" , ['privatekey' => $privatekey ]  );
        }catch(Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage());
        }
    }

    function get_balanceAction(){
        $params = $this->getParams();
        $Business = Common::ImportBusiness("CollectQrcode" ,"Admin" );
        try{
            $data = $Business->get_balance( $params );
            Common::EchoResult(1 ,  "操作成功" , $data  );
        }catch(Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage());
        }
    }

}