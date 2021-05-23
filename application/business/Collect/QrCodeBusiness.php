<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 */
use Pheanstalk\Pheanstalk;
class QrCodeBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.*  from collect_qrcode as a   {$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from collect_qrcode as a   {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['content']) && $params['content']){
            $params['content'] =  addslashes($params['content'] );
            $getWhere.=" and a.`content` = '{$params['content']}' ";
        }
        if(isset($params['user_id']) && $params['user_id']){
            $getWhere.=" and a.`user_id` = '{$params['user_id']}' ";
        }

        if(isset($params['qr_id']) && $params['qr_id']){
            $params['qr_id'] = intval($params['qr_id']);
            $getWhere.=" and a.`qr_id` = '{$params['qr_id']}' ";
        }


        //$getWhere.=" and a.`status`  in (0,1,2,3)  ";
        if(isset($params['status'])  AND  $params['status']!= 99 ){
            $params['status'] = intval($params['status']);
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }

        if(isset($params['auto_match'])  AND  $params['auto_match']!= 99 ){
            $params['auto_match'] = intval($params['auto_match']);
            $getWhere.=" and a.`auto_match` = '{$params['auto_match']}' ";
        }
        return $getWhere;
    }


    public function doAddQrCode( $params = []  ){
        $Source_QrCode = new Source_QrCode();
        $CollectQrcodeModel = new CollectQrcodeModel();
        $user_id = $params['user_id'];
        $path =  isset($params['path']) ? trim($params['path']) :"" ;
        $content =  isset($params['content']) ? trim($params['content']) :"" ;
        $chain_name =  isset($params['chain_name']) ? trim($params['chain_name']) :"" ;
        $remark =  isset($params['remark']) ? trim($params['remark']) :"" ;
        if( empty($chain_name )){
            throw new \Exception("请选择币种");
        }
        /*if( empty($path)){
            throw new \Exception("请上传图片");
        }*/
        if( empty($content)){
            throw new \Exception("请输入充币地址");
        }
        $Source_USTDTRC = new Source_USTDTRC();
        $s = $Source_USTDTRC->validateaddress($content);
        if(!$s){
            throw new \Exception("币地址不正确");
        }
        $info = $CollectQrcodeModel->getInfo(['content' => $content]);
        if( !empty($info) ){
            if( !in_array($info['status'] , [-1 , -2 ] ) ){
                throw new \Exception("充币地址已经存在！！,请更换地址");
            }
        }
        //二维码图片解析
        /*$s_content =  $Source_QrCode->decodeQr(APP_PATH . "/public/{$path}" , $path);
        if( empty($s_content)){
            throw new \Exception("没有解析出二维码内容，请稍后重新尝试！！");
        }*/

        //生成新的二维码
        $qrData = $this->makeAndParsePic( $content);

        $path = isset($qrData['path']) ? $qrData['path'] :"";
        if( empty($path)){
            throw new \Exception("查询解析二维码生成错误。");
        }
        $insert = [
            'user_id'=>$user_id,
            'content' => $content,
            'path' => $path,
            'create_time' => date("Y-m-d H:i:s"),
            'update_time' => date("Y-m-d H:i:s"),
            'status' => 1 ,
            'chain_name' =>$chain_name ,
            'remark' => $remark,
        ];
        $qr_id = $CollectQrcodeModel->insertData($insert);
        if( !$qr_id ){
            return array('code' => 0 , 'msg' => "添加失败请稍后" );
        }
        return array('code' => 1 , 'msg' => "添加成功");
    }
    //根据图片的远程地址 解析二维码图片的内容 并且重新生成一个图片地址
    private function makeAndParsePic( $text = ''  )
    {
        include_once(APP_PATH . '/application/library/qrcode/phpqrcode.php');
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 6;//生成图片大小
        $t = time() . "_" . rand(1000, 99999);
        $folder = APP_PATH . "/public/Uploads/qrcode/";
        if( !file_exists($folder)){
            mkdir($folder , 0777 , true );
        }
        $QR = $folder  . $t . ".new.png";
        \QRcode::png($text, $QR, $errorCorrectionLevel, $matrixPointSize, 2);
        return [
            'text' => $text,
            'path' =>  'Uploads/qrcode/' . $t . ".new.png"
        ];
    }

    public function auto_match( $params ){
        $user_id = isset($params['user_id'])?trim($params['user_id']): 0 ;
        $auto_match = isset($params['auto_match'])?intval($params['auto_match']): 0 ;
        $qr_id = isset($params['qr_id'])?intval($params['qr_id']): 0 ;
        if( empty($user_id) ){
            throw new Exception("参数缺失");
        }
        if( empty($qr_id) ){
            throw new Exception("缺少二维码ID");
        }
        $model = new CollectQrcodeModel();
        $info = $model->getInfo( array('user_id' => $user_id , 'qr_id' => $qr_id  ) );
        if( empty($info) ){
            throw new Exception( "没有查询到币地址！！");
        }
        $request = array(
            'auto_match' => $auto_match ,
        );
        $status   = $model->updateData($request , ['user_id' => $user_id, 'qr_id' => $qr_id] );
        if( !$status ){
            throw new Exception( "修改失败请稍后");
        }
        return true ;
    }

    public function getBeanstaldObj(){
        global $_G ;
        require_once  APP_PATH . '/application/library/Beanstalk/vendor/autoload.php' ;
        $host = $_G['config']['beanstald']['host'];
        $port = $_G['config']['beanstald']['port'];
        $pheanstalk = new Pheanstalk($host  , $port);
        return $pheanstalk ;
    }


}