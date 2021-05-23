<?php
class Admin_AuthController extends Yaf_Controller_Abstract
{
    public function indexAction(){
        $expire = time() + 3600 * 3 ;
        setcookie("admin_is_auth" , 1 , $expire) ;
        echo "<h1>认证成功.....</h1>";
    }
}
