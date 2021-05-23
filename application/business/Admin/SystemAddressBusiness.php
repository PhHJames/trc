<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 */
class SystemAddressBusiness extends AbstractModel {
    //usdt 转账
    public function do_transfer_to( $params = []  ){
        global $_G;
        $Source_QrCode = new Source_QrCode();
        $CollectQrcodeModel = new CollectQrcodeModel();
        $Source_USTDTRC = new Source_USTDTRC();
        $Source_Account = new Source_Account();
        $from_address =  isset($params['from_address']) ? trim($params['from_address']) :"" ;
        $to_address =  isset($params['to_address']) ? trim($params['to_address']) :"" ;
        $remark =  isset($params['remark']) ? trim($params['remark']) :"" ;
        $amount =  isset($params['amount']) ? trim($params['amount']) : 0  ;
        $password =  isset($params['password']) ? trim($params['password']) : ""  ;
        if( empty($from_address ) or empty($to_address) or empty($amount)){
            throw new \Exception("数据提交不完整");
        }
        $fromData = $Source_Account->getSummaryAccountByAddress($from_address);
        if( empty($fromData) ){
            throw new \Exception("没有获取到系统归集账号");
        }
        $private_key = isset($fromData['private_key']) ? $fromData['private_key'] :"";
        if( empty($fromData) ){
            throw new \Exception("没有获取到系统归集账号私钥");
        }
        if( empty($password )){
            throw new \Exception("请输入授权密码");
        }
        if($password != $_G['config']['setting']['auth_password'] ){
            throw new \Exception("授权密码错误");
        }
        $AddressTransCommon = Common::ImportBusiness("AddressTransCommon" , "Common");
        $s_remark = "归集账号USDT资金转出($remark)" ;
        $res = $AddressTransCommon->trc20_trans($private_key , $from_address  , $to_address , $amount , $s_remark );
        return $res ;
    }
    //TRX币转出
    public function do_transfer_trx_to( $params = []  ){
        global $_G;
        $Source_QrCode = new Source_QrCode();
        $CollectQrcodeModel = new CollectQrcodeModel();
        $Source_USTDTRC = new Source_USTDTRC();
        $Source_Account = new Source_Account();
        $from_address =  isset($params['from_address']) ? trim($params['from_address']) :"" ;
        $to_address =  isset($params['to_address']) ? trim($params['to_address']) :"" ;
        $remark =  isset($params['remark']) ? trim($params['remark']) :"" ;
        $amount =  isset($params['amount']) ? trim($params['amount']) : 0  ;
        $password =  isset($params['password']) ? trim($params['password']) : ""  ;
        if( empty($from_address ) or empty($to_address) or empty($amount)){
            throw new \Exception("数据提交不完整");
        }
        if( empty($password )){
            throw new \Exception("请输入授权密码");
        }
        if($password !=  $_G['config']['setting']['auth_password'] ){
            throw new \Exception("授权密码错误");
        }
        $fromData = $Source_Account->getTrxByAddress($from_address);
        if( empty($fromData) ){
            throw new \Exception("没有获取到系统TRX账号");
        }
        $private_key = isset($fromData['private_key']) ? $fromData['private_key'] :"";
        if( empty($fromData) ){
            throw new \Exception("没有获取到系统TRX账号私钥");
        }
        $AddressTransCommon = Common::ImportBusiness("AddressTransCommon" , "Common");
        $res = $AddressTransCommon->trx_trans($private_key , $from_address  , $to_address , $amount,$remark);
        return $res ;
    }

}