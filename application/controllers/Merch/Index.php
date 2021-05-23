<?php  
class Merch_IndexController extends Merch_CenterController {
    public function init() {
        parent::init();
    }
    public function indexAction(){
        $menu =  $this->getMenu();
        $allowData = Source_Sysconfig::getInstance()->getAllowPlace();
        $is_allow = 0 ;
        if( in_array($this->userInfo['mid'] ,$allowData )){
            $is_allow = 1 ;
        }
        $this->displayTemplate('index/index' , array(
            'menu' => $menu ,
            'userInfo'=>$this->userInfo,
            'is_allow' => $is_allow
        ) );
    }
    public function homeAction(){
        global $_G ;
        $MerchantModel = new MerchantModel();
        $account = $MerchantModel->getInfo( array('mid' => $this->userInfo['mid']) );
        $str = explode("," , $account['white_list_ip'] );
        $white_list_ip = implode("\n" , $str);
        $account['white_list_ip'] = $white_list_ip ;
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