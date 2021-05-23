<?php
/**
 * Created by 不要复制我的代码.
 * User: 不要复制我的代码
 * Date: 2019/5/14 0014
 * Time: 上午 11:45
 */
class AddressTransLogModel extends AbstractModel{
    protected $table = "address_trans_log" ;
    protected $dbIndex = 0  ;
    public function addTrans( $data = [] ){
        $data['create_date'] = date("Y-m-d H:i:s" , time() );
        $txid = isset($data['txid']) ? $data['txid'] : '';
        if( $txid ){
            $info = $this->getInfo(['txid' => $txid ]);
            if( $info ){
                return true ;
            }
        }
        return $this->insertData($data);
    }
}