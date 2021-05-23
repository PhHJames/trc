<?php

/*
初始化 给出文件需要存储的路径  是否是随机文件名  允许的最大直 支持的类型 需要压缩的大小

第一步判断上传的文件是否包含错误如果包含错误则输出错误

第二步判断给定的路径是否存在如果不存在则新建文件夹


第三步判断文件的真是的类型


第四步上传文件的大小如果超过了设定的大小则给出提示如果是图片则不用判断大小直接压缩掉

第五步保存 判断目标文件是否存在如果村则则需要随机一个文件名保存

*/


class UploadImage
{

    const VERSION = "0.1";

    private $overwrite_file = false; //存在是否重写

    private $errorinfo = "";

    //字段的名称
    private $filed;

    //最大20480000
    private $maxsize;

    //允许的类型
    private $allowedMimeTypes;

    private $targetPath;

    private $maxwidth =0 ;


    private $maxheight=0 ;

    private $extData = array();
    private $mime_helping = array('text' => array('text/plain',),
        'image' => array(
            'image/jpeg',
            'image/jpg',
            'image/pjpeg',
            'image/png',
            'image/gif',
        ),
        'document' => array(
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-excel',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.presentation',
            'application/pdf',
            'application/vnd.ms-office',
            'text/plain'
        ),
        'video' => array(
            'video/3gpp',
            'video/3gpp',
            'video/x-msvideo',
            'video/avi',
            'video/mpeg4',
            'video/mp4',
            'video/mpeg',
            'video/mpg',
            'video/quicktime',
            'video/x-sgi-movie',
            'video/x-ms-wmv',
            'video/x-flv',
        ),
    );

    public function setInput($filed)
    {
        $this->filed = $filed;
    }

    public function setMaxFileSize($size)
    {
        $this->maxsize = $size;

    }

    public function setAllowedMimeTypes(array $config)
    {
        $this->allowedMimeTypes = $config;
    }

    public function setTargetPath($path)
    {
        $this->targetPath = $path;

    }

    public function setConfig()
    {


    }

    public function  setResetSize($width ,$heigth)
    {
        $this->maxwidth =$width ;
        $this->maxheight = $heigth ;

    }
    public function getExtName($file)
    {
        $ret = pathinfo($file);
        return $ret["extension"];
    }
    public function setExtData($extData = array() ){
        $this->extData = $extData ;
    }
    public function getMimeType($file)
    {

        if (function_exists("finfo_open")) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            return finfo_file($finfo, $file['path']);
        } else {
            return $file['type'];
        }
    }

    private function randFileName()
    {                                  //uniqid() 函数基于以微秒计的当前时间，生成一个唯一的 ID
        return md5(uniqid(mt_rand())); //mt_rand() 使用 Mersenne Twister 算法返回随机整数
    }

    private function createDir($path)
    {
        if (!is_dir($path)) { //如果目录不存在，mkdir 创建目录
            return mkdir($path, 0777, true);
        }
        return true;
    }

    private function is_writable($path)
    {
        return is_writable($path);
    }

    /***压缩图片**/
    private function resizeImage($file, $maxwidth, $maxheight,  $filetype)
    {

        if ($filetype == "jpg") {
            $im = ImageCreateFromJpeg($file);
        } elseif ($filetype == "png") {
            $im = imagecreatefrompng($file);
        } elseif ($filetype == "gif") {
            return true;
        } else {
            $this->errorinfo = "只支持png jpg gif";
            return false;
        }

        $pic_width = imagesx($im);
        $pic_height = imagesy($im);

        if (($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
            if ($maxwidth && $pic_width > $maxwidth) {
                $widthratio = $maxwidth / $pic_width;
                $resizewidth_tag = true;
            }

            if ($maxheight && $pic_height > $maxheight) {
                $heightratio = $maxheight / $pic_height;
                $resizeheight_tag = true;
            }

            if ($resizewidth_tag && $resizeheight_tag) {
                if ($widthratio < $heightratio)
                    $ratio = $widthratio;
                else
                    $ratio = $heightratio;
            }

            if ($resizewidth_tag && !$resizeheight_tag)
                $ratio = $widthratio;
            if ($resizeheight_tag && !$resizewidth_tag)
                $ratio = $heightratio;

            $newwidth = $pic_width * $ratio;
            $newheight = $pic_height * $ratio;

            if (function_exists("imagecopyresampled")) {
                $newim = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            } else {
                $newim = imagecreate($newwidth, $newheight);
                imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            }
            if ($filetype == "jpg") {
                 imagejpeg($newim,$file);
            } elseif ($filetype == "png") {
                 imagepng($newim,$file);
            } else
            {
                 imagegif($newim,$file);
            }
            imagedestroy($newim);
        }
    }


    private function fileinfo($filed)
    {
        if (empty($_FILES) or empty($_FILES[$filed])) {
            return null;
        }
        $file = $_FILES[$filed];
        if (!empty($_FILES[$filed]["error"])) {
            $this->errer = $_FILES[$filed]["error"];
            return false;
        }
        return ["name" => $file["name"], "size" => $file["size"], "path" => $file['tmp_name'], "type" => $file['type']];
    }

    private function getExtNmae($ext)
    {
        $config = [
                    'image/jpeg' => "jpg",
                    'image/jpg' => "jpg",
                    'image/pjpeg' => "jpg",
                    'image/png' => "jpg",
                    'image/gif' => "gif",
                    'application/msword'=>"doc",
                    'application/pdf'=>"pdf",                                    
                    'application/zip'=>"zip",                                    
                    'application/rar'=>"rar",                                    
                    'application/x-msexcel'=>"xls",
                    'application/x-excel'=>"xls",
                    'application/x-msexcel'=>"xls",
                    'application/excel'=>"xls",
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>'docx',
                    'application/kswps'=>"wps",
                    'application/vnd.ms-excel'=>"csv",
                    'text/x-c' =>"csv",
                    'text/x-csv' =>"csv",
                    'application/vnd.ms-office' => 'xls',
                    'text/plain' => 'csv',
        ];
        $configData  =  array_merge($config , $this->extData) ;
        return isset($configData[$ext]) ? $configData[$ext] : '' ;

    }

    public  function  getErrorInfo()
    {
        return $this->errorinfo;
    }
    private function movieFile($file, $target)
    {
        return move_uploaded_file($file, $target);  //将上传的文件移动到新位置
    }

    public function save()
    {

        if (!is_dir($this->targetPath)) { //测试文件是否为目录
            $flage = $this->createDir($this->targetPath);
            if (!$flage) {
                $this->errorinfo = "网络繁忙请稍后再试";
                return false;
            }
        }

        if (!$this->is_writable($this->targetPath)) { //判断指定的文件是否可写
            $this->errorinfo = "网络繁忙请稍后再试";
            return false;
        }
        
        $file = $this->fileinfo($this->filed);
        if (empty($file)) {//没有获取到文件的相关信息
            $this->errorinfo = "你上传的文件有问题,请检测文件的格式,文件大小";
            return false;
        }
        
        //判断大小
        if ($file["size"] > $this->maxsize) {
            $this->errorinfo = "您上传的文件太大了";
            return false;
        }
        
        //判断类型
        $ext = $this->getMimeType($file);
        if (!in_array($ext, $this->allowedMimeTypes)) {
            $this->errorinfo = "文件类型错误,请检测文件的格式,当前文件格式为：{$ext}";
            return false;
        }
        $type = $this->getExtNmae($ext);
        
        //随机数，微妙，再MD5生成的文件名
        $targefile = $this->randFileName();
        $target = $this->targetPath . $targefile . "." . $type;
        
        //将上传的文件移动到新位置
        $flage = $this->movieFile($file["path"], $target);
        if ($flage) {
            //echo "width:".$this->maxwidth."-----height:".$this->maxheight;
            if($this->maxwidth>0 and $this->maxheight>0)
            {
                $this->resizeImage($target, $this->maxwidth, $this->maxheight,  $type);//压缩图片
            }
            return $targefile . "." . $type;
        }
        return false;
    }

}
 


