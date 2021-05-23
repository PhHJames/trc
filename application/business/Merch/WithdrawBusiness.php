<?php
/**
 * Created by Awe.
 * User: Administrator
 * Date: 2019/10/20 0020
 * Time: 18:29
 */
class WithdrawBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.* ,b.name as w_name from merchants_withdraw as a left join merchants_bank as b 
  
        on a.bank_id = b.id
        {$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from merchants_withdraw as a left join merchants_bank as b 
  
        on a.bank_id = b.id {$getWhere} " ;
        $data =  $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
        $list = $data['list'];
        $total = $data['total'];
        return array('list' => $list , 'total' => $total );
    }

    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['order_no']) && $params['order_no']){
            $getWhere.=" and a.`order_no` = '{$params['order_no']}' ";
        }
        if(isset($params['merch_order']) && $params['merch_order']){
            $getWhere.=" and a.`merch_order` = '{$params['merch_order']}' ";
        }
        if(isset($params['status']) && $params['status']){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        if(isset($params['mid']) && $params['mid']){
            $getWhere.=" and a.`mid` = '{$params['mid']}' ";
        }
        return $getWhere;
    }
    public function doWithdraw( $params= []  , $mid  = 0 , $come_from  = 0 ,$merch_order = '' ){
        $bank_id = isset($params['bank_id'])?trim($params['bank_id']):'';
        $money = isset($params['money'])?trim($params['money']):'';
        $notify_url = isset($params['notify_url'])?trim($params['notify_url']):'';
        if( empty($bank_id)){
            throw  new Exception("请选择目标币地址");
        }
        if( empty($money)){
            throw  new Exception("请输入提现金额");
        }
        $fen =  ($money * pow(10 , NUMBER_FORMAT) ) ;
        if( $fen  <= 0 ){
            throw new Exception( "金额格式不对" );
        }       
        $MerchantModel = new MerchantModel();
        $MerchantsBankModel = new MerchantsBankModel();
        $MerchantWithDrawModel = new MerchantWithDrawModel();
        $MerchantMoneyLogModel = new MerchantMoneyLogModel();
        $merchInfo = $MerchantModel->getInfo( array('mid' => $mid ) , true  );

        $min_withdraw_money = trim($merchInfo['min_withdraw_money']);//最低提现金额
        if( $fen < $min_withdraw_money * pow(10 , NUMBER_FORMAT) ){
            throw new Exception( "最低提现金额必须大于 {$min_withdraw_money}" );
        }
        $bankInfo = $MerchantsBankModel->getInfo( array('id' =>$bank_id , 'mid' => $mid , 'status' => 2   ) );
        if( empty($bankInfo ) ){
            throw new Exception( "没有查询到相关的币地址信息" );
    
        if($bankInfo['status'] != 2 ){
            throw new Exception( "你选择的币没有审核通过" );
        }
        if(!empty($merch_order)){
            $merchOrderInfo = $MerchantWithDrawModel->getInfo(['merch_order' => $merch_order]);
            if(!empty($merchOrderInfo)){
                throw new Exception( "提现单号已经存在 请更换。" );
            }
        }
        $object = $this->db(0);
        try{
            $object->beginTransaction();
            $merchInfo = $MerchantModel->getInfo( array('mid' => $mid ) , true  );
            if( empty($merchInfo ) ){
                throw new Exception( "没有查询到商户信息" );
            }
            if($merchInfo['status'] != 1 ){
                throw new Exception( "商户暂时不允许提现，登录账号异常" );
            }
            if( $merchInfo['money'] <= 0 ){
                throw new Exception( "账户金额不足" );
            }
            //需要从商户账户里面扣除的钱为
            $shouxufei = $merchInfo['fixed_poundage']  ; //提现的手续费
            $need_money = $money + $shouxufei ;
            /*echo $need_money ;
            echo "<h1>";
            echo $merchInfo['money'];
            die();*/
            if( $need_money > $merchInfo['money'] ){
                throw new Exception( "账户金额不足!!!" );
            }
            $subtract_money = $money - $merchInfo['fixed_poundage'] ;
            if($subtract_money <= 0 ){
                //throw new Exception( "提现币数量小于等于手续费" );
            }
            $order_no = Tools::build_order_no(32);
            $withDraw = array(
                'mid' => $mid ,
                'money' => $money ,
                'create_time' => date("Y-m-d H:i:s" , time() ),
                'update_time' => date("Y-m-d H:i:s" , time() ),
                'fixed_poundage' => $merchInfo['fixed_poundage'],
                'status' => 1 ,
                'order_no' => $order_no,
                'bank_id'=> $bankInfo['id'],
                'come_from' => $come_from,
                'notify_url' => $notify_url
             );
            if(!empty($merch_order)){
                 $withDraw['merch_order'] = $merch_order;
            }
            $id = $MerchantWithDrawModel->insertData($withDraw);
            if(!$id ){
                throw new Exception( "写入提现数据失败" );
            }
            //更改商户的钱
            $now_money =  $merchInfo['money'] - $need_money ;
            $now_freez_money = $merchInfo['freez_money'] + $need_money ;
            $sql = "update merchants set money = $now_money ,freez_money = '{$now_freez_money}'  where mid = '{$mid}' ";
            $update = $object->Exec($sql);
            if( !$update ){
                throw new Exception( "修改商户的金额出错" );
            }
            //插入资金日志
            $logInsert = array(
                'mid'=>$mid ,
                'money' => -$need_money ,
                'account_money' => $merchInfo['money'],
                'now_money' => $now_money,
                'type'=> 1,
                'create_time' => date("Y-m-d H:i:s" , time() ),
                'update_time' => date("Y-m-d H:i:s" , time() ),
                'remark' => "商户提现手续费:".($shouxufei),
            );
            $id = $MerchantMoneyLogModel->insertData($logInsert);
            if(!$id ){
                throw new Exception( "保存商户资金日志数据失败" );
            }
            $object->commit();
            return $order_no ;
        }catch(Exception $e ){
            $object->rollBack();
            throw new Exception( "提现失败！！" . $e->getMessage() );
        }
    }

    //获取商户允许使用提现的银行卡
    public function getMerchWithdrawCard($mid ){
        $sql = "select a.* from merchants_bank as a where a.status in ( 2 ) and a.mid = '{$mid}'  " ;
        return $this->db( 0 )->find( $sql );
    }
}