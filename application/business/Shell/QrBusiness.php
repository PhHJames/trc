<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/23 0023
 * Time: 21:03
 */
class QrBusiness extends AbstractModel {
    /*public function money(){
        $sql = "select * from collect_qrcode where status  not in ( -1 )  order  by qr_id desc  ";
        $list = $this->db(0)->find($sql);
        if( empty($list )){
            echo "【". date("Y-m-d H:i:s" , time() ). "】:没有币地址："  . "\n";
            return ;
        }
        $begin_date = time();
        $Source_USTDTRC = new Source_USTDTRC();
        $CollectQrcodeModel = new CollectQrcodeModel();
        $success = 0 ;
        foreach ($list as $kk => $vv ){
            try{
                $accountData = $Source_USTDTRC->getAccountMoney($vv['content']  );
                if( empty($accountData )){
                    continue ;
                }
                $usdt = isset($accountData['usdt']) ? $accountData['usdt'] : 0 ;
                $trx = isset($accountData['trx']) ? $accountData['trx'] : 0 ;
                $CollectQrcodeModel->updateData(['trx' => $trx , 'usdt' => $usdt , 'update_time' => date("Y-m-d H:i:s") ] , ['qr_id' => $vv['qr_id'] ] );
                echo "【". date("Y-m-d H:i:s" , time() ). "】:更新系统收款码id：{$vv['qr_id']}：币地址：{$vv['content']} ， trx：{$trx} ， usdt ：{$usdt}  成功" .  "\n";
                $success++;
                usleep(1000000);
            }catch( \Exception $e ){
                echo "【". date("Y-m-d H:i:s" , time() ). "】:更新系统收款码id：{$vv['qr_id']} ， 错误了 "  .$e->getMessage() .  "\n";
            }
        }
        $end_date = time();
        $diff = $end_date - $begin_date ;
        echo "【". date("Y-m-d H:i:s" , time() ). "】:更新收款地址余额success ,更新数量：{$success} , 消耗时间：{$diff}" .  "\n";
    }*/
    public function addressToRedis(){
        while(1){
            $begin_date = time();
            $redis_key = "system_address";
            $redisHandler  = RedisClass::getInstance(15)->handler ;
            $Source_Account = new Source_Account();
            $hash = $Source_Account->getSystemAddress();
            $count_sql = "select count(*) as c from collect_qrcode   ";
            $total_data = $this->db(0)->find($count_sql);
            $total = isset($total_data[0]['c']) ? $total_data[0]['c'] : 0 ;
            $pageSize = 5000 ;
            $ceil = ceil( $total / $pageSize );
            $success = 0 ;
            for($index = 1 ; $index <= $ceil ; $index ++ ){
                $limit = ($index - 1 ) * $pageSize . " , " . $pageSize ;
                $sql = "select content as address from collect_qrcode limit {$limit} ";
                $list = $this->db(0)->find($sql);
                if( !empty($list )){
                    foreach ($list as $kk => $vv ){
                        $status = $redisHandler->sAdd($redis_key, $vv['address'] );
                        if($status){
                            $success ++ ;
                        }
                    }
                }
            }
            foreach ($hash as  $item ){
                $status = $redisHandler->sAdd($redis_key, $item );
                if($status){
                    $success ++ ;
                }
            }
            $end_date = time();
            $diff = $end_date - $begin_date ;
            echo "【". date("Y-m-d H:i:s" , time() ). "】:系统中的币地址添加到redis ,更新数量：{$success} , 消耗时间：{$diff}" .  "\n";
            sleep(30 );
        }
    }

    public function resetIndex(){
        $time = date("Y-m-d H:i:s",time());
        $sql = "update collect_qrcode set match_index = 5000 , update_time = '{$time}' ";
        $this->db(0)->Exec($sql);
        echo "【". date("Y-m-d H:i:s" , time() ). "】:修改收款地址的指数为5000" .  "\n";
    }
}