<?php
/**
 * Created by 不要改老子的用户名.
 * User: 不要改老子的用户名
 * Date: 2021/2/2 0002
 * Time: 11:48
 */
class BlockTransBusiness extends AbstractModel {
    //trx trans消息
    public function do_trx_trans($data = [] ){
        $this->checkRsyncWhiteIp();
        $from = isset($data['owner_address'])?$data['owner_address']:"";
        $to = isset($data['to_address'])?$data['to_address']:"";
        $txid = isset($data['txID'])?$data['txID']:"";
        $amount = isset($data['amount'])?$data['amount']:"";
        $extra = isset($data['extra'])?$data['extra']:"";
        if( empty($from) or empty($to) ){
            throw new Exception("缺失地址： from 或者 to 没有 " );
        }
        $this->checkTransid($txid);
        $this->checkAddress($from , $to );
        $AddressTransLogModel = new AddressTransLogModel();
        $AddressTransLogModel->addTrans(
            [
                'from' => $from ,
                'to' => $to ,
                'amount' => $amount ,
                'remark' => "系统检测到有TRX交易"  ,
                'txid' => $txid ,
                'channel' =>  'TRX'  ,
            ]
        );
        $this->updateCollectMoney($from,$to);
        return true ;
    }
    //usdt trans消息
    public function do_usdt_trans($data = []){
        $this->checkRsyncWhiteIp();
        $from = isset($data['owner_address'])?$data['owner_address']:"";
        $to = isset($data['to_address'])?$data['to_address']:"";
        $txid = isset($data['txID'])?$data['txID']:"";
        $amount = isset($data['amount'])?$data['amount']:"";
        $extra = isset($data['extra'])?$data['extra']:"";
        if( empty($from) or empty($to) ){
            throw new Exception("缺失地址： from 或者 to 没有 " );
        }
        $this->checkTransid($txid);
        $this->checkAddress($from , $to );
        $AddressTransLogModel = new AddressTransLogModel();
        $AddressTransLogModel->addTrans(
            [
                'from' => $from ,
                'to' => $to ,
                'amount' => $amount ,
                'remark' => "系统检测到有USDT交易"  ,
                'txid' => $txid ,
                'channel' =>  'USDT'  ,
            ]
        );
        $this->updateCollectMoney($from,$to);
        return true ;
    }
    //检测交易id是否存在
    public function checkTransid( $trx_id ){
        $AddressTransLogModel =new AddressTransLogModel();
        $info = $AddressTransLogModel->getInfo(['txid' => $trx_id]);
        if( !empty($info )){
            throw new Exception("交易id：{$trx_id} ，已经存在");
        }
    }
    //检测地址
    public function checkAddress($from , $to ){
        $Source_Account = new Source_Account();
        $systemAddress = $Source_Account->getSystemAddress();
        if( in_array($from ,$systemAddress )){
            return true ;
        }
        if( in_array($to ,$systemAddress )){
            return true ;
        }
        $CollectQrcodeModel = new CollectQrcodeModel();
        $qrInfo = $CollectQrcodeModel->getInfo(['content' => $from ]) ;
        if( !empty($qrInfo )){
            return true ;
        }
        $qrInfo = $CollectQrcodeModel->getInfo(['content' => $to ]) ;
        if( !empty($qrInfo )){
            return true ;
        }
        throw  new \Exception("地址：{$to} ,或者地址：{$from} 不合法 ， 不是系统的  ");
    }

    public function checkRsyncWhiteIp(){
        $ip = Network::GetClientIp();
        $rsync_white_ips = Source_Sysconfig::getInstance()->getVal('rsync_white_ip');
        if( empty($rsync_white_ips)){
            throw  new \Exception("系统暂无配置白名单IP");
        }
        $rsync_white_ip = explode("\n" ,$rsync_white_ips );
        $hash = [] ;
        foreach ($rsync_white_ip as $val ){
            if( !empty($val )){
                $hash[] = $val ;
            }
        }
        if( in_array($ip , $hash )){
            return true ;
        }
        throw  new \Exception("ip:{$ip} 不在白名单：{$rsync_white_ips} ");
    }
    //更新系统二维码的余额
    public function updateCollectMoney($from , $to){
        $CollectQrcodeModel = new CollectQrcodeModel();
        $Source_USTDTRC = new Source_USTDTRC();
        $qrInfo = $CollectQrcodeModel->getInfo(['content' => $from ]) ;
        if( !empty($qrInfo )){
            $address = $qrInfo['content'];
            $moneyData = $Source_USTDTRC->getAccountMoney($address);
            $usdt = $moneyData['usdt'];
            $trx = $moneyData['trx'];
            $CollectQrcodeModel->updateData(
                ['trx' =>$trx ,
                    'usdt' => $usdt , 'update_time'=>date("Y-m-d H:i:s")  ],['qr_id' => $qrInfo['qr_id']]);
        }
        $qrInfo = $CollectQrcodeModel->getInfo(['content' => $to ]) ;
        if( !empty($qrInfo )){
            $address = $qrInfo['content'];
            $moneyData = $Source_USTDTRC->getAccountMoney($address);
            $usdt = $moneyData['usdt'];
            $trx = $moneyData['trx'];
            $CollectQrcodeModel->updateData(
                ['trx' =>$trx ,
                    'usdt' => $usdt , 'update_time'=>date("Y-m-d H:i:s")  ],['qr_id' => $qrInfo['qr_id']]);
        }
        return true ;
    }
}