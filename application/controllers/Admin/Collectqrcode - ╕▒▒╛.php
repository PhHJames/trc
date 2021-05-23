<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_CollectqrcodeController extends Admin_BaseAuthController {
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
        $res = array(
            'pageSize'=> 10,
            'statusData' => Enum::$QrStatus,
            'SummaryAccount'=> $SummaryAccount,
            'TrxList' => $TrxList,
        );
        $this->displayTemplate('collect_qrcode/index' , $res);
    }
    private function getAjaxList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("CollectQrcode" ,"Admin" );
        $data = $Business->getCollectQrcodeList($params,$pageSize);
        $list = $data['list'];
        $QrStatus = Enum::$QrStatus;
        global $_G;
        if( !empty($list) ){
            foreach ($list as $key => $value) {
                $statusData =  Enum::getVal($value['status'] ,$QrStatus );
                $list[$key]['statusString'] = $statusData['value'];
                //$list[$key]['c_money'] = "￥". $value['c_money'] / 100 ;
                $list[$key]['path_pic'] = $_G['config']['domain']['domain']['UPLOADS_HOST'] . $value['path'];
                unset($list[$key]['privatekey']);
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }

    public function addAction(){
        if($this->getRequest()->isPost()){
            $this->doAddQrCode();
            exit;
        }
        global $_G;
        $ChannelModel = new ChannelModel();
        $allChannel = $ChannelModel->getChannel( " AND status = 1 ");
        $Source_Account = new Source_Account();
        $trxList = $Source_Account->getTrxList();
        $res = [
            'allChannel' => $allChannel ,
            'trxList' => $trxList
        ] ;
        $this->displayTemplate('collect_qrcode/qr_add' , $res);
    }

    private function doAddQrCode(){
        set_time_limit(0);
        $params = $_POST;
        $Business = Common::ImportBusiness("CollectQrcode" ,"Admin" );
        try{
            $res  = $Business->doAddQrCode( $params);
            $success_num = isset($res['success_num']) ? $res['success_num'] : 0 ;
            $error_num = isset($res['error_num']) ? $res['error_num'] : 0 ;
            $error_msg = isset($res['error_msg']) ? $res['error_msg'] :  ''  ;
            Common::EchoResult(1 , "成功添加：{$success_num} 个 失败：{$error_num}个 <br>失败信息：{$error_msg} <br> 激活之后才可以匹配订单" );
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
    //批量转入trx
    public function beatch_trans_trxAction(){
        global $_G;
        $params = $this->getParams();
        $amout = isset($params['amount']) ? $params['amount'] : 0 ;
        $qr_ids = isset($params['qr_ids']) ? $params['qr_ids'] :  ''  ;
        $finish = isset($params['finish']) ? $params['finish'] :  ''  ;
        $address = isset($params['address']) ? $params['address'] :  ''  ;
        $password = isset($params['password']) ? $params['password'] :  ''  ;
        if( empty($amout )){
            $this->showError("系统提示" , "没有转入金额");
        }
        if( empty($address )){
            $this->showError("系统提示" , "没有目标账户");
        }
        $ar_arr  = explode("," , $qr_ids  ) ;
        if($finish == 1 ){
            echo "<h1>批量转入TRX数据已经执行完毕！</h1>";
            exit;
            $this->showError("系统提示" , "批量转入TRX数据已经执行完毕！");
        }
        if( empty($ar_arr) ){
            $this->showError("系统提示" , "参数错误！");
        }

        $qr_id = array_pop($ar_arr );
        if( empty($qr_id) ){
            $this->showError("系统提示" , "没有币地址！");
        }
        if( empty($password )){
            $this->showError("系统提示" , "请输入授权密码！");
        }
        if($password != $_G['config']['setting']['auth_password'] ){
            $this->showError("系统提示" , "授权密码错误！");
        }
        //开始执行
        $Business = Common::ImportBusiness("CollectQrcode" ,"Admin" );
        try{
            $Business->recharge_trx($address , $qr_id , $amout );
        }catch(Exception $e ){
            echo "<h1>充值TRX出错了：" . $e->getMessage() . "</h1>";
        }
        $str = implode("," ,$ar_arr ) ;
        $finish = (empty($ar_arr)) ? 1 : 0 ;
        $this->echo_now("正在执行批量转入TRX数据 请等待,请不要关闭浏览器");
        $url = "/Admin_Collectqrcode/beatch_trans_trx?qr_ids=$str&amount=$amout&finish=$finish&address={$address}&password={$password}";
        echo "<script>location.href='{$url}'</script>";
        //sleep(6);
    }

    //批量转出usdt
    public function beatch_trans_usdtAction(){
        global $_G;
        $params = $this->getParams();
        $amout = isset($params['amount']) ? $params['amount'] : 0 ;
        $qr_ids = isset($params['qr_ids']) ? $params['qr_ids'] :  ''  ;
        $finish = isset($params['finish']) ? $params['finish'] :  ''  ;
        $address = isset($params['address']) ? $params['address'] :  ''  ;
        $password = isset($params['password']) ? $params['password'] :  ''  ;
        if( empty($amout )){
            //$this->showError("系统提示" , "没有转入金额");
        }
        if( empty($address )){
            $this->showError("系统提示" , "没有目标账户地址");
        }
        $ar_arr  = explode("," , $qr_ids  ) ;
        if($finish == 1 ){
            echo "<h1>批量转入USDT数据已经执行完毕！</h1>";
            exit;
           // $this->showError("系统提示" , "批量转入USDT数据已经执行完毕！");
        }
        if( empty($ar_arr) ){
            $this->showError("系统提示" , "参数错误！");
        }

        $qr_id = array_pop($ar_arr );
        if( empty($qr_id) ){
            $this->showError("系统提示" , "没有币地址！");
        }
        if( empty($password )){
            $this->showError("系统提示" , "请输入授权密码！");
        }
        if($password != $_G['config']['setting']['auth_password'] ){
            $this->showError("系统提示" , "授权密码错误！");
        }
        $CollectQrcodeModel = new CollectQrcodeModel();
        $qrInfo = $CollectQrcodeModel->getInfo(['qr_id' => $qr_id ]);
        if( empty( $qrInfo )){
            $this->showError("系统提示" , "没有获取到二维码信息！");
        }
        $amout = $qrInfo['usdt'];
        //开始执行
        $Business = Common::ImportBusiness("CollectQrcode" ,"Admin" );
        try{
            $Business->trans_usdt($qr_id , $address  , $amout );
        }catch(Exception $e ){
            echo "<h1>用户钱包USDT转出出错了：" . $e->getMessage() . "</h1>";
        }


        $str = implode("," ,$ar_arr ) ;
        $finish = (empty($ar_arr)) ? 1 : 0 ;
        $this->echo_now("正在执行批量转出USDT数据 请等待,请不要关闭浏览器");
        $url = "/Admin_Collectqrcode/beatch_trans_usdt?qr_ids=$str&amount=$amout&finish=$finish&address={$address}&password={$password}";
        echo "<script>location.href='{$url}'</script>";
        //sleep(6);
    }
    private function echo_now($msg){
        //ob_end_clean();
        echo str_repeat(" ",1024);
        echo "<h1><center>" . date('H:i:s').'='.$msg,"</center></h1><hr>";
        echo '<script>window.scrollTo(0,document.body.offsetHeight+document.body.scrollTop)</script>';
        flush();
        usleep(100000);
    }

}