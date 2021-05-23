<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2021/1/16 0016
 * Time: 20:35
 */

class Source_USTDTRC{
    public $req_url = '' ;
    public $API_KEY = '';
    public function __CONSTRUCT(){
        global $_G;
        $this->req_url = $_G['config']['domain']['domain']['USDT_API_DOMAIN'];
        $this->API_KEY = $_G['config']['trongrid']['API_KEY'];
    }
    /**
     * 获取账户历史TRC20交易记录
     * @return
     */

    public function getTrcTransactionsListByAddress( $address = '' ,$max_num = 200 ){
        $url = "https://api.trongrid.io/v1/accounts/{$address}/transactions/trc20?only_confirmed=true&only_to=true&only_confirmed=true&limit=$max_num&order_by=block_timestamp%2Cdesc";
        $resp = $this->RequestData($url);
        $result = json_decode($resp , true );
        return $result ;
    }
    /**
     * 判断 USTD 收款地址的格式是否正确
     * @return
     */
    public function validateaddress( $address = '' ){
        $url = $this->req_url . "/isAddress";
        $resp = $this->sendPostRawReq($url , ['address' => $address ] );
        Log_Db::WriteLog("validateaddress" , "检测地址: {$address} 返回：" . $resp ) ;
        $result = json_decode($resp , true );
        $status  = isset($result['data']['status'])?$result['data']['status']: '' ;
        return ($status == 'true' ) ? true : false ;
    }


    /**
     * 查询一个账号的信息, 包括余额, TRC10 余额, 冻结资源, 权限等.
     * @return
     */
    public function GetAccount($address = '' ){
        $url = $this->req_url . "/get_account";
        $data = ['address' => $address ];
        $resp = $this->sendPostRawReq($url , $data );
        Log_Db::WriteLog("GetAccount" , "查询账户: {$address} 返回：" . $resp ) ;
        $result = json_decode($resp , true );
        return $result ;
    }




    /**
     * 查询一个账号的TRX余额 和 usdt 余额 调用nodejs服务
     * 由于调用nodejs服务获取trx这个有缓存不及时 所以直接调用线上的API
     * @return
     */
    public function getAccountMoney($address = ''){

        $url = $this->req_url . "/get_money";
        $data = ['address' => $address ];
        $resp = $this->sendPostRawReq($url , $data );
        Log_Db::WriteLog("getAccountMoney" , "查询账户: {$address} 返回：" . $resp ) ;
        $result = json_decode($resp , true );

        $usdt = isset($result['data']['usdt']) ? $result['data']['usdt'] : 0 ;
        $usdt = $usdt / pow(10 , 6 ) ;


        $req_trx_url = "https://api.trongrid.io/wallet/getaccount";
        $str =  "{\"address\" : \"{$address}\" , \"visible\":true}" ;
        $resp_trx = $this->sendPostRawJsonReq($req_trx_url , $str );

        $result_trx = json_decode($resp_trx , true );
        $trx = isset($result_trx['balance']) ? $result_trx['balance'] : 0 ;
        $trx = $trx / pow(10 , 6 ) ;

        $hash = [
            'usdt' => $usdt ,
            'trx' => $trx ,
        ];
        return $hash ;
    }

    /**
     * 生成一个本地的 TRC20 账号 ， 这个账号是未激活的
     * @return
     */
    public function geneateLocalAccount(){
        $url = $this->req_url . "/generate_address";
        $data = [];
        $resp = $this->sendPostRawReq($url , $data );
        Log_Db::WriteLog("geneateLocalAccount" , "生成一个本地账户失败： 返回：" . $resp ) ;
        $result = json_decode($resp , true );
        return $result ;
    }
    /**
     * 生成一个在线的 TRC20 账号 账号是激活的
     * @return
     */
    public function geneateOnlineAccount($from_address_private = ''){
        $url = $this->req_url . "/generate_address_online";
        $data = ['from_address_private' => $from_address_private ];
        $resp = $this->sendPostRawReq($url , $data );
        Log_Db::WriteLog("geneateOnlineAccount" , "生成一个线上账户失败： 返回：" . $resp ) ;
        $result = json_decode($resp , true );
        return $result ;
    }
    /**
     * 查询交易的信息
     * @return
     */
    public function GetTransactionById( $tx_id = '' ){
        $url = $this->req_url . "/GetTransactionById";
        $data = ['trxid' => $tx_id ];
        $resp = $this->sendPostRawReq($url , $data );
        Log_Db::WriteLog("GetTransactionById" , "查询交易信息失败： 返回：" . $resp ) ;
        $result = json_decode($resp , true );
        return $result ;
    }
    /**
     * 查询交易的信息 根据交易id 查询是否付款成功
     * @return
     */
    public function checkTransIsSuccess( $txid = '' ){
        $info = $this->GetTransactionById( $txid );
        $contractRet = isset($info['data']['ret'][0]['contractRet'])?$info['data']['ret'][0]['contractRet']:"";
        if($contractRet == 'SUCCESS' ){
            return true ;
        }
        return false ;
    }


    /**
     * Trc20 转账
     * @return
     */
    public function trc20_trans($from_address_private = '' , $fromAddress = '' , $toAddress = '' , $ammout =  0  ){
        $url = $this->req_url . "/trc20_trans";
        $data = [
            'from_address_private' => $from_address_private,
            'fromAddress' => $fromAddress,
            'toAddress' => $toAddress,
            'amount' => $ammout,

        ];
        $resp = $this->sendPostRawReq($url , $data );
        Log_Db::WriteLog("trc20_trans" , "Trc20 转账： 返回：" . $resp ) ;
        $result = json_decode($resp , true );
        return $result ;
    }


    /**
     * TRX 转账
     * @return
     */
    public function trx_trans($from_address_private = '' , $fromAddress = '' , $toAddress = '' , $ammout =  0  ){
        $url = $this->req_url . "/trx_trans";
        $data = [
            'from_address_private' => $from_address_private,
            'fromAddress' => $fromAddress,
            'toAddress' => $toAddress,
            'amount' => $ammout,

        ];
        $resp = $this->sendPostRawReq($url , $data );
        Log_Db::WriteLog("trx_trans" , "Trx 转账： 返回：" . $resp ) ;
        $result = json_decode($resp , true );
        return $result ;
    }
    public  function sendPostRawReq($url , $data = array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POST,           1 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,      json_encode($data , JSON_UNESCAPED_UNICODE) );
        curl_setopt($ch, CURLOPT_HTTPHEADER,   [
            'Content-Type: application/json',
            'TRON-PRO-API-KEY:'.$this->API_KEY
        ]);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result ;
    }

    public  function sendPostRawJsonReq($url , $str = '' ){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POST,           1 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,      $str);
        curl_setopt($ch, CURLOPT_HTTPHEADER,   [
            'Content-Type: application/json',
            'TRON-PRO-API-KEY:'.$this->API_KEY
        ]);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result ;
    }

    public function RequestData($url,$data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS,  http_build_query($data));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $output = curl_exec($curl);
        curl_close($curl);
        curl_setopt($curl, CURLOPT_HTTPHEADER,    [
            'TRON-PRO-API-KEY:'.$this->API_KEY
        ]);
        return $output;
    }
}