<?php  
class Collect_IndexController extends Collect_CenterController {
    public function init() {
        parent::init();
    }
    public function indexAction(){
        $menu =  $this->getMenu();
        $this->displayTemplate('index/index' , array(
            'menu' => $menu ,
            'userInfo'=>$this->userInfo,
        ) );
    }
    public function homeAction(){
        global $_G ;
        $CollectUserModel = new CollectUserModel();
        $account = $CollectUserModel->getInfo( array('user_id' => $this->userInfo['user_id']) );
        $api_domain = $_G['config']['domain']['domain']['API_DOMAIN'] ;
        $api_domain = rtrim($api_domain , "/");
        $this->displayTemplate('index/main' , array(
            'userInfo'=>$this->userInfo,
            'account' => $account ,
            'api_domain' => $api_domain,
        ) );
    }
}
?>