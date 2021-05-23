<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-03-26 14:57:33
 */
class Collect_QrController extends Collect_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxOrderList();
            exit;
        }
        /*$Business = Common::ImportBusiness("Order" ,"Merch" );
        $tData = $Business->orderTotal($this->userInfo['user_id']);*/
        $res = array(
            'pageSize'=> 10 ,
            'statusData' => Enum::$QrStatus,
        );
        $this->displayTemplate('qr/qr_index' , $res);
    }
    private function getAjaxOrderList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("QrCode" ,"Collect" );
        $params['user_id'] = $this->userInfo['user_id'];
        $data = $Business->getList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            global $_G;
            $QrStatus = Enum::$QrStatus ;
            foreach ($list as $key => $value) {
                $statusData = Enum::getVal($value['status'] ,$QrStatus )  ;
                $list[$key]['status_string'] =  isset($statusData['value']) ? $statusData['value'] : ""  ;
                $list[$key]['path_pic'] = $_G['config']['domain']['domain']['UPLOADS_HOST'] . $value['path'];
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
        $res = [
            'allChannel' => $allChannel ,
        ] ;
        //print_r($allChannel);
        $this->displayTemplate('qr/qr_add' , $res);
    }

    private function doAddQrCode(){

        $params = $_POST;
        $Business = Common::ImportBusiness("QrCode" ,"Collect" );
        try{
            $params['user_id'] = $this->userInfo['user_id'];
            $res  = $Business->doAddQrCode( $params);
            Common::EchoResult(1 , "添加成功" );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }

    public function uploadAction(){
        global $_G ;
        $day =date("Ymd",time());
        $baseUploadPath = $_G['config']['folder']['folder']['upload_path'].DIRECTORY_SEPARATOR ;
        $folder =  'qr' ;
        $rootPath = $baseUploadPath.$folder;
        $path = $rootPath.DIRECTORY_SEPARATOR.$day.DIRECTORY_SEPARATOR;
        $upload = new UploadImage();
        $upload->setInput( "file" );
        //$upload->setMaxFileSize("20480000");
        $upload->setMaxFileSize("2097152");//2M 大小吧
        $upload->setAllowedMimeTypes(array('image/jpeg' , 'image/jpg' ,  'image/png' ));
        $upload->setTargetPath($path);
        $file = $upload->save();
        if(!$file){
            $errorMsg = $upload->getErrorInfo();
            Common::EchoResult(0 , $errorMsg );
        }
        $filename = "Uploads/{$folder}/{$day}/{$file}";
        Common::EchoResult(1 , "上传成功" ,  array('filename'=>$filename , 'src' =>$_G['config']['domain']['domain']['UPLOADS_HOST'] .$filename ) );
    }

    public function auto_matchAction(){
        $params = $this->getParams();
        $params['user_id'] = $this->userInfo['user_id'];
        $Business = Common::ImportBusiness("QrCode" ,"Collect" );
        try{
            $data = $Business->auto_match( $params );
            Common::EchoResult(1 ,  "操作成功" );
        }catch(Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage());
        }
    }

}