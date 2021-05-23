<?php
/**
 * @Author: Awe
 * @Date:   2016-06-04 22:20:44
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2016-06-04 22:42:02
 */
class UeController extends CenterController {
    public function init() {
        parent::init();
    }
    public function indexAction(){
        require_once APP_PATH."/application/library/UE/controller.php";
    }
}