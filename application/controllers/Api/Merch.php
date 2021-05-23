<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/19 0019
 * Time: 21:13
 */
class Api_MerchController extends Api_CommonController
{
    public function init(){
        parent::init();
    }
   
    //获取商户的资金余额
    public function queryMoneyAction(){
        $channel = "USDT_TRC20";
        $Business = Common::ImportBusiness("Orders" ,"Api" );
        //if( empty()){}
        $params = $this->getParams(1) ;
        try{
            $Source_Merch = new Source_Merch();
            $data =  $Source_Merch->checkSign($params);
            $rate_data = $Business->getMoneyRateData($channel);
            $money = $data['money'];
            $r_money = $money * $rate_data['f_rate'];
            $r_money = Tools::number_format($r_money , NUMBER_FORMAT );
            
            $freez_money = $data['freez_money'];
            $r_freez_money= $freez_money * $rate_data['f_rate'];
            $r_freez_money = Tools::number_format($r_freez_money , NUMBER_FORMAT );
            
            $result = [
                'money' => $money ,
                'r_money' => $r_money ,
                'freez_money'=>$freez_money,
                'r_freez_money'=>$r_freez_money
            ];
            //print_r($rate_data);
            $this->echoJson( 1, "查询成功" , $result );
        }catch(Exception $e ){
            $this->echoJson( $e->getCode(),$e->getMessage() );
        }
    }
    
   

}