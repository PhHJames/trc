<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/24 0024
 * Time: 7:55
 */
class Api_ChannelController extends Api_CommonController
{
    public function init(){
        parent::init();
    }
    public function listAction(){
        $Business = Common::ImportBusiness("Channel" ,"Api" );
        $params = $this->getParams(1) ;
        try{
            $data = $Business->listChannel($params);
            $this->echoJson( 1, "OK" , $data );
        }catch(Exception $e ){
            $this->echoJson( $e->getCode(),$e->getMessage() );
        }
    }
}