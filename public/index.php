<?php
date_default_timezone_set('Asia/Shanghai');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Credentials : true');
header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie');
header('Access-Control-Max-Age:1728000');
header('Content-type: text/html; charset=utf-8');
header('X-Powered-By:golang');
define('APP_PATH', realpath(dirname(__FILE__) . '/../'));
define('API_CODE_NAME', 'code');
define('API_MSG_NAME', 'msg');
define('NUMBER_FORMAT', 0x8);
//domainCheck();
$var_21825 = new Yaf_Application(APP_PATH . '/conf/application.ini');
$var_21825->bootstrap();
$var_21825->run();
function domainCheck()
{
    $var_21826 = preg_match('/cli/i', php_sapi_name()) ? true : false;
    if ($var_21826) {
        return true;
    }
    $var_21827 = trim($_SERVER['SERVER_NAME']);
    $var_21828 = array('trc.yunshipay.com', 'usdt.pay.com', 'erc.yunshipay.com');
    $var_21827 = trim($var_21827);
    if (!in_array($var_21827, $var_21828)) {
        exit('The domain name  is not authorized. Please contact the service provider');
    }
}