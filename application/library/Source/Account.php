<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2021/1/16 0016
 * Time: 20:35
 */

class Source_Account{
    public $account =  [];
    public function __CONSTRUCT(){
        global $_G;
        $this->account =  @include APP_PATH . '/conf/account.php';
    }
    /**
     * 获取系统配置的Trx账号 里面包含了 余额信息
     * @return
     */
    public function getTrxList( ){
        $trx = isset($this->account['trx']) ? $this->account['trx'] : []  ;
        if( empty($trx )){
            return [];
        }
        $Source_USTDTRC = new Source_USTDTRC();
        foreach ($trx as $k => $v ){
            $info = $Source_USTDTRC->getAccountMoney( $v['address'] );
            $trx[$k]['money'] = $info ;
        }
        return $trx;
    }

    /**
     * 获取系统配置的归集账号 里面包含了 余额信息
     * @return
     */
    public function getSummaryAccountList( ){
        $SummaryAccount = isset($this->account['SummaryAccount']) ? $this->account['SummaryAccount'] : []  ;
        if( empty($SummaryAccount )){
            return [];
        }
        $Source_USTDTRC = new Source_USTDTRC();
        foreach ($SummaryAccount as $k => $v ){
            $info = $Source_USTDTRC->getAccountMoney( $v['address'] );
            $SummaryAccount[$k]['money'] = $info ;
        }
        return $SummaryAccount;
    }

    /**
     *  根据系统内置的 TRX地址 获取相关信息
     * @return
     */
    public function getTrxByAddress( $address = '' ){
        $trx = isset($this->account['trx']) ? $this->account['trx'] : []  ;
        if( empty($trx )){
            return [];
        }
        $hash = [];
        foreach ($trx as $k => $v ){
            if( $v['address'] == $address  ){
                $hash = $v ;
                break;
            }
        }
        return $hash;
    }

    /**
     *  根据系统内置的 归集地址 获取相关信息
     * @return
     */
    public function getSummaryAccountByAddress( $address = '' ){
        $trx = isset($this->account['SummaryAccount']) ? $this->account['SummaryAccount'] : []  ;
        if( empty($trx )){
            return [];
        }
        $hash = [];
        foreach ($trx as $k => $v ){
            if( $v['address'] == $address  ){
                $hash = $v ;
            }
        }
        return $hash;
    }

    /**
     * 获取系统配置的内置账号 包含了 trx 和 usdt 返回数组
     * @return
     */
    public function getSystemAddress(){
        $hash = [];
        $SummaryAccount = isset($this->account['SummaryAccount']) ? $this->account['SummaryAccount'] : []  ;
        foreach ($SummaryAccount as $k => $v ){
            $hash[] = $v['address'];
        }
        $trx = isset($this->account['trx']) ? $this->account['trx'] : []  ;
        foreach ($trx as $sk => $sv ){
            $hash[] = $sv['address'];
        }
        return $hash;
    }



}