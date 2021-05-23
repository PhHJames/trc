<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-04-26 22:59:32
 * @des 商户 业务文件
 */
class ChannelBusiness extends AbstractModel {
    //下单
    public function listChannel($params = array() ){
        //检测签名
        $merch = $this->checkSign($params);
        //白名单
        $this->checkWhiteIp($merch);
        $MerchantFeeModel = new MerchantFeeModel();
        $channel = $MerchantFeeModel->getMerchFee($merch['mid']);
        if( empty($channel )){
            return [] ;
        }
        $hash = array();
        foreach ($channel as $key => $val ){
            $data = array(
                'channel' => $val['channel'] ,
                'name' => $val['alias'] ,
                'fee' => $val['fee'] ,
                'channel' => $val['channel'] ,
                'min_money' => Common::foramtNumber($val['min_money'] /100 , 2 )  ,
                'max_money' => Common::foramtNumber($val['max_money'] /100 , 2 ) ,
            );
            $hash[] = $data;
        }
        return array('list' => $hash , 'count' => intval(count($hash)) );
    }
    //检测签名,如果成功返回商户数据
    public function checkSign($params){
        $Source_Merch = new Source_Merch();
        return $Source_Merch->checkSign($params);
    }
    //白名单检测
    public function checkWhiteIp( $merch ){

    }


}