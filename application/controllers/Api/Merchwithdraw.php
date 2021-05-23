<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/19 0019
 * Time: 21:13
 */
class Api_MerchwithdrawController extends Api_CommonController
{
    public function init(){
        parent::init();
    }
    //申请提现
    public function applyAction(){
        $Business = Common::ImportBusiness("MerchWithdraw" ,"Api" );
        $params = $_REQUEST ;
        global $_G;
        try{
            $data = $Business->apply($params);
            $resp = [
                'order_no' => $data['order_no'],
                'merch_order' => $data['merch_order'],
            ];
            $this->echoJson( 1, "提现申请成功，请稍后等待结果" , $resp );
        }catch(Exception $e ){
            $this->echoJson( $e->getCode(),$e->getMessage() );
        }
    }
    //提现查询
    public function queryAction(){
        $Business = Common::ImportBusiness("MerchWithdraw" ,"Api" );
        $params = $this->getParams(1);
        $order_no = isset($params['order_no']) ? $params['order_no'] :'';
        $merch_order = isset($params['merch_order']) ? $params['merch_order'] :'';

        //if( empty()){}
        $params = $this->getParams(1) ;
        try{
            $data = $Business->query($params);
            $this->echoJson( 1, "查询成功" , $data );
        }catch(Exception $e ){
            $this->echoJson( $e->getCode(),$e->getMessage() );
        }
    }


}