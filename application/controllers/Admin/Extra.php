<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2016-06-04 23:32:39
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-09 17:13:41
 * @备注此文件是不需要验证权限的
 */
class ExtraController extends CenterController {
    public function init() {
        $this->NotLoginExclusion = ["uploadCsv"] ;
        $this->NotAuthExclusion=["uploadCsv"];
        parent::init();
    }
    //上传图片----这个是普通的上传 form表单上传
    public function uploadAction(){
        global $_G ;
        $day =date("Ymd",time());
        $baseUploadPath = $_G['config']['folder']['folder']['upload_path'].DIRECTORY_SEPARATOR ;
        $type = (isset($_POST['type'])) ? $_POST['type'] : 1 ;
        //这个地方配置下 ， 可以公用这个上传图片的代码的哦
        //###############只需要传递type ，然后配置文件夹名称即可哈
        $tpath = array(
            2 => 'sxb_admin/user_pic' ,//后台图像
            3 => "activity/zc",//专场活动上传的图片
            4 => "activity/weixinqr" , //微信的群二维码
            5 =>"bbs/thread_icon",//社区的封面图
            6 => 'bbs/thread_icon' , //主题的图标 缩略图
            7 => "mianjing",
        );
        $folder = (isset($tpath[$type])) ? $tpath[$type] : 'newspic' ;
        $rootPath = $baseUploadPath.$folder;
        $path = $rootPath.DIRECTORY_SEPARATOR.$day.DIRECTORY_SEPARATOR;
        $upload = new UploadImage();
        $upload->setInput( "file" );
        //$upload->setMaxFileSize("20480000");
        $upload->setMaxFileSize("2097152");//2M 大小吧
        $upload->setAllowedMimeTypes(array('image/jpeg' , 'image/jpg' , 'image/pjpeg' , 'image/png' ,'image/gif' ));
        $upload->setTargetPath($path);
        $file = $upload->save();
        if(!$file){
            $errorMsg = $upload->getErrorInfo();
            Common::EchoResult(0 , $errorMsg );
        }
        $filename = "Uploads/{$folder}/{$day}/{$file}";
        Common::EchoResult(1 , "上传成功" ,  array('filename'=>$filename , 'src' =>UPLOADS_HOST .$filename ) );
    }

}
