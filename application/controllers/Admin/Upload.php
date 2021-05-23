<?php

/**
 * 上传文件图片
 * @Author: Chl
 * @Date:   2016-07-25 16:32:39
 * @Last Modified by: Vincent
 * @Last Modified time: 2017-12-08 14:32:39
 * @备注此文件是不需要验证权限的
 */
class uploadController extends CenterController {

    public function init() {
        parent::init();
    }

    //上传图片
    public function uploadimageAction() {
        $day = date("Ymd", time());
        $config = new Yaf_Config_Ini(APP_PATH . "/conf/application.ini");
        $baseUploadPath = $config['folder']['folder']['upload_path'] . DIRECTORY_SEPARATOR;
        $type = isset($_POST['type']) ? $_POST['type'] : 1;
        //这个地方配置下 ， 可以公用这个上传图片的代码的哦
        //###############只需要传递type ，然后配置文件夹名称即可哈
        $tpath = array(
            1 => 'newspic', //咨询上传图片
            2 => 'cms_admin_pic', //cms的后台图像
            3 => 'adpic', //广告
            4 => 'class', //类别图片
            5 =>'expevent', //体验活动图片
            6 =>'logo', //公司logo图片
            7 =>'certificate', //营业执照图片、身份证图片
            8 => 'Personal_photos', //个人照片
            9 => 'act',//空中宣讲
            10 => "exp/list" , //体验上传的图片
            11 => "exp/catalog" , //体验栏目上传的图片
            12 => "exp/cs" , //体验客服上传的图片
            13 => "resumeshop/detail" , //简历商城的详情图片
        );
        $folder = (isset($tpath[$type])) ? $tpath[$type] : 'newspic';
        $rootPath = $baseUploadPath . $folder;
        $path = $rootPath . DIRECTORY_SEPARATOR . $day . DIRECTORY_SEPARATOR;
        $upload = new UploadImage();
        $upload->setInput("file");
        $upload->setMaxFileSize("2097152"); //2M 大小吧
        $upload->setAllowedMimeTypes(array('image/jpeg' , 'image/jpg' , 'image/pjpeg' , 'image/png' , 'image/gif' ));
        $upload->setTargetPath($path);
        $file = $upload->save();
        if (!$file) {
            $errorMsg = $upload->getErrorInfo();
            Common::EchoResult(0, $errorMsg);
        }
        $filename = "Uploads/{$folder}/{$day}/{$file}";
        //echo $filename;
        Common::EchoResult(1, "上传成功", array('filename' => $filename,'UPLOADS_HOST'=>UPLOADS_HOST));
    }

    public function uploadeditorAction() {
        $day = date("Ymd", time());
        $config = new Yaf_Config_Ini(APP_PATH . "/conf/application.ini");
        $baseUploadPath = $config['folder']['folder']['upload_path'] . DIRECTORY_SEPARATOR;
        $type = isset($_POST['type']) ? $_POST['type'] : 1;
        //这个地方配置下 ， 可以公用这个上传图片的代码的哦
        //###############只需要传递type ，然后配置文件夹名称即可哈
        $tpath = array(
            1 => 'newspic', //咨询上传图片
            2 => 'cms_admin_pic', //cms的后台图像
            3 => 'adpic', //广告
            4 => 'class', //类别图片
            5 =>'expevent', //体验活动图片
            6 =>'logo', //公司logo图片
            7 =>'certificate', //营业执照图片、身份证图片
            8 => 'Personal_photos', //个人照片
            9 => 'act',//空中宣讲
            10 => "exp/list" , //体验上传的图片
            11 => "exp/catalog" , //体验栏目上传的图片
            12 => "exp/cs" , //体验客服上传的图片
        );
        $folder = (isset($tpath[$type])) ? $tpath[$type] : 'newspic';
        $rootPath = $baseUploadPath . $folder;
        $path = $rootPath . DIRECTORY_SEPARATOR . $day . DIRECTORY_SEPARATOR;
        $upload = new UploadImage();
        $upload->setInput("file");
        $upload->setMaxFileSize("2097152"); //2M 大小吧
        $upload->setAllowedMimeTypes(array('image/jpeg' , 'image/jpg' , 'image/pjpeg' , 'image/png' , 'image/gif' ));
        $upload->setTargetPath($path);
        $file = $upload->save();
        if (!$file) {
            $errorMsg = $upload->getErrorInfo();
            Common::EchoResult(0, $errorMsg);
        }
        $filename = "Uploads/{$folder}/{$day}/{$file}";
        //echo $filename;
        echo json_encode(array('location' => UPLOADS_HOST.$filename));
    }

    //上传附件
    public function uploadattachmentAction() {
        $day = date("Ymd", time());
        $config = new Yaf_Config_Ini(APP_PATH . "/conf/application.ini");
        $baseUploadPath = $config['folder']['folder']['upload_path'] . DIRECTORY_SEPARATOR;
        $type = isset($_POST['type']) ? $_POST['type'] : 1;
        //这个地方配置下 ， 可以公用这个上传附件的代码的哦
        //###############只需要传递type ，然后配置文件夹名称即可哈
        $tpath = array(
            1 => 'attachment', //上传的附件地址
        );
        $folder = (isset($tpath[$type])) ? $tpath[$type] : 'attachment';
        $rootPath = $baseUploadPath . $folder;
        $path = $rootPath . DIRECTORY_SEPARATOR . $day . DIRECTORY_SEPARATOR;
        $upload = new UploadImage();
        $upload->setInput("file");
        $upload->setMaxFileSize("20971520"); //200M 大小吧
        $upload->setAllowedMimeTypes(array('image/jpeg' , 'image/jpg' , 'image/pjpeg' , 'image/png' , 'image/gif' , 'application/pdf' , 'application/zip' , 'application/rar'));
        $upload->setTargetPath($path);
        $file = $upload->save();
        if (!$file) {
            $errorMsg = $upload->getErrorInfo();
            Common::EchoResult(0, $errorMsg);
        }
        $filename = "Uploads/{$folder}/{$day}/{$file}";
        //echo $filename;
        Common::EchoResult(1, "上传成功", array('filename' => $filename,'UPLOADS_HOST'=>UPLOADS_HOST));
    }
}
