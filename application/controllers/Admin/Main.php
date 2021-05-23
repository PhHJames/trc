<?php
class MainController extends CenterController {
	public function init() {
		parent::init();
	}
	public function indexAction(){
        $this->displayTemplate('index/main' , 
            array(
                'userInfo' => $this->userInfo,
            ) 
        );
	}
}