<?php
/**
 * Created by 不要复制我的代码.
 * User: 不要复制我的代码
 * Date: 2019/5/14 0014
 * Time: 上午 11:45
 */
class CollectQrcodeModel extends AbstractModel{
    protected $table = "collect_qrcode" ;
    protected $dbIndex = 0  ;
    public function queryCollectStatis( $where = '' ){
        $sql = "select sum(trx) as trx from collect_qrcode where  1 = 1 {$where}  limit 1";
        $info = $this->db(0)->findOne($sql);
        $trx = isset($info['trx'])?$info['trx']:0;
        $sql = "select sum(usdt) as usdt from collect_qrcode where  1 = 1 {$where}  limit 1";
        $info = $this->db(0)->findOne($sql);
        $usdt = isset($info['usdt'])?$info['usdt']:0;
        return [
            'trx' => $trx ,
            'usdt' => $usdt 
        ];
    }
}