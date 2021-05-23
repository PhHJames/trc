<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-03-26 14:57:33
 */
class Merch_SdkController extends Merch_CommonController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        $res =  [] ;
        $this->displayTemplate('sdk/index' , $res);
    }

    public function successAction(){
        echo "success";
    }

}