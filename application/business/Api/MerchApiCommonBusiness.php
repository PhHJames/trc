<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-04-26 22:59:32
 * @des 商户 业务文件
 */

class MerchApiCommonBusiness extends AbstractModel {
    /**
     * @des 检测商户的签名 如果成功返回商户数据
     */
    public function checkSign($params = [] ){
        $Source_Merch = new Source_Merch();
        return $Source_Merch->checkSign($params);
    }
    /**
     * @des 白名单验证
     */
    public function checkWhiteIp( $merch = []  ){
        $client_ip = Network::GetClientIp();
        if( empty($client_ip )){
            throw new \Exception("没有获取到商户的请求IP地址");
        }
        $merchIpData = explode("," , $merch['white_list_ip'] );
        foreach ($merchIpData as $k =>$val){
            $merchIpData[$k]=trim($val);
        }
        if( empty($merchIpData )){
            throw new \Exception("商户暂未配置白名单 请先配置白名单");
        }
        if(!in_array($client_ip ,$merchIpData )){
            throw new \Exception("ip:{$client_ip} , 不在白名单内");
        }
        return true ;
    }
}