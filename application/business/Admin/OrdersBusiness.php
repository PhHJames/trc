<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 */
class OrdersBusiness extends AbstractModel {
    public function getOrdersList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getOrderWhere($params);
        $sql = "select a.* , b.account as m_account , b.phone as m_phone  ,c.name as c_name  from orders as a left join merchants as b 
on a.mid = b.mid  left join channel as c on a.channel = c.channel {$getWhere} order by a.create_date desc "   ;
        $sql_count = "select count(*) as c   from orders as a left join merchants as b on a.mid = b.mid left join channel as c on a.channel = c.channel {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getOrderWhere($params=array()){
        $getWhere = "";
        if(isset($params['mid']) && $params['mid']){
            $getWhere.=" and a.`mid` = '{$params['mid']}' ";
        }
        if(isset($params['order_sn']) && $params['order_sn']){
            $getWhere.=" and a.`order_sn` = '{$params['order_sn']}' ";
        }
        if(isset($params['merch_order_sn']) && $params['merch_order_sn']){
            $getWhere.=" and a.`merch_order_sn` = '{$params['merch_order_sn']}' ";
        }
        if(isset($params['out_order_sn']) && $params['out_order_sn']){
            $getWhere.=" and a.`out_order_sn` = '{$params['out_order_sn']}' ";
        }
        if(isset($params['trans_order_sn']) && $params['trans_order_sn']){
            $getWhere.=" and a.`trans_order_sn` = '{$params['trans_order_sn']}' ";
        }
        if(isset($params['txid']) && $params['txid']){
            $getWhere.=" and a.`txid` = '{$params['txid']}' ";
        }

        if(isset($params['account']) && $params['account']){
            $getWhere.=" and b.`account` = '{$params['account']}' ";
        }
        if(isset($params['status']) AND  $params['status'] ){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }

        if(isset($params['is_supplement']) AND  $params['is_supplement'] ){
            $getWhere.=" and a.`is_supplement` = '{$params['is_supplement']}' ";
        }

        if(isset($params['date_range']) AND  $params['date_range'] ){
            @list( $begin_date , $end_date )  = explode("~" , $params['date_range']);
            $begin_date = trim($begin_date);
            $end_date = trim($end_date);
            $getWhere.=" and DATE_FORMAT(a.create_date,'%Y-%m-%d' ) >= '{$begin_date}' and DATE_FORMAT(a.create_date,'%Y-%m-%d' )  <= '{$end_date}' ";
            //$getWhere.=" and a.`is_supplement` = '{$params['is_supplement']}' ";
        }
        
        if(isset($params['address']) && $params['address']){
            $getWhere.=" and a.`address` = '{$params['address']}' ";
        }
        return $getWhere;
    }
    //补单操作
    public function doSupplementh( $params ){
        $id = isset($params['id'])?intval($params['id']):'';
        $money = isset($params['money'])?trim($params['money']):'';
        $txid = isset($params['txid'])?trim($params['txid']):'';
        $MerchantModel = new MerchantModel();
        $MerchantMoneyLogModel =  new MerchantMoneyLogModel();
        $AgentMoneyLogModel = new AgentMoneyLogModel();
        $ordersModel = new OrdersModel();
        $OrdersSupplementsModel = new OrdersSupplementsModel();
        $CollectQrcodeModel = new CollectQrcodeModel();
        $AgentModel = new AgentModel();
        //金额判断
        if( empty($money) ){
            throw new Exception( "请输入金额"  );
        }
        $money = floatval($money);
        if( $money * pow(10 , NUMBER_FORMAT)  <= 0 ) {
            throw new Exception("输入的金额错误");
        }

        $OrdersModel = new OrdersModel();
        $info  = $OrdersModel->getInfo( array('id' => $id  ) );
        if( empty($info )){
            throw new Exception( "没有查询到订单信息"  );
        }


        $Source_Exchange = new Source_Exchange();
        $f_rate =  $Source_Exchange->CnyUSDTHuansuan(2 , $info['channel']);

        if( empty($f_rate)){
            throw new \Exception( "没有计算出汇率" , 0  );
        }
        $f_rate = Tools::number_format($f_rate , NUMBER_FORMAT );



        $rate =  $Source_Exchange->CnyUSDTHuansuan(1, $info['channel']);

        if( empty($rate)){
            throw new \Exception( "没有计算出汇率" , 0  );
        }
        $rate = Tools::number_format($rate , NUMBER_FORMAT );



        $object = $this->db(0);
        try{
            $now_date = date("Y-m-d H:i:s" ,time() ) ;
            $object->beginTransaction();
            $orderInfo = $ordersModel->getInfo( array('id' => $id ) , true  );
            if( empty($orderInfo)){
                throw new Exception( "订单不存在"  );
            }
            if( ! in_array($orderInfo['status']  , array(1,3) ) ){
                throw new Exception( "此订单不允许补单"  );
            }
            $mid = $orderInfo['mid'];
            $m_fee = $orderInfo['m_fee'];
            $merch_order_sn = $orderInfo['merch_order_sn'];
            $order_sn = $orderInfo['order_sn'];
            $agent_id = $orderInfo['agent_id'];
            $a_fee = $orderInfo['a_fee'];
            //查询商户的表
            $merchInfo = $MerchantModel->getInfo(array('mid' => $mid) , true  );
            if( empty($merchInfo) ){
                throw new Exception( "没有查询到商户的信息" );
            }
            $qrInfo = $CollectQrcodeModel->getInfo(['qr_id' =>$orderInfo['qr_id'] ] , true );
            if( empty($qrInfo) ){
                throw new Exception( "没有查询到二维码信息" );
            }
            $qr_status = $qrInfo['status'];
            $real_money =Tools::number_format ( ($money *  (1 - $m_fee )) , NUMBER_FORMAT) ;//实际到账多少
            $r_money = Tools::number_format($money * $f_rate , NUMBER_FORMAT );
            //修改订单数据表
            $update = array(
                'status' =>  2  ,
                'complete_date' => date("Y-m-d H:i:s" ,time() ) ,
                'money' => $money ,
                'real_money'=>$real_money,
                'is_supplement' => 1 ,
                'e_rate' => $rate ,
                'f_rate' => $f_rate ,
                'r_money' => $r_money
            );
            if($txid){
                $update['txid'] = $txid ;
            }
            if( $agent_id > 0 ){
                $agent_real_money = Tools::number_format ( ($money *  ($m_fee - $a_fee )) , NUMBER_FORMAT) ;
                $update['agent_real_money'] = $agent_real_money;
            }
            $p_real_money = $money - $real_money - $agent_real_money ;
            $update['p_real_money'] = $p_real_money;//平台赚的钱
            $updateStatus = $ordersModel->updateData($update,array('id' => $id ) );
            if( !$updateStatus ){
                throw new Exception( "修改订单数据失败" );
            }
            $CollectQrcodeModel->updateData(['status' =>1, 'update_time' => date("Y-m-d H:i:s" ,time())   ] , ['qr_id' => $qrInfo['qr_id'] ] );
            $this->db(0)->Exec("Update collect_qrcode set match_index = match_index - 1 where qr_id = '{$qrInfo['qr_id']}' ");
            //给商户加钱
            $now_money =  $merchInfo['money'] + $real_money ;
            $sql = "update merchants set money = $now_money , update_time = '{$now_date}'  where mid = '{$mid}'  ";
            $update = $object->Exec($sql);
            if( !$update ){
                throw new Exception( "修改商户的金额出错" );
            }
            //插入资金日志
            $logInsert = array(
                'mid'=>$mid ,
                'money' => $real_money ,//变动的资金
                'account_money' => $merchInfo['money'],
                'now_money' => $now_money,
                'type'=> 5,
                'create_time' => date("Y-m-d H:i:s" , time() ),
                'update_time' => date("Y-m-d H:i:s" , time() ),
                'remark' => "系统补单,商户订单号是:{$merch_order_sn} 返还商户:".$real_money,
            );
            $id = $MerchantMoneyLogModel->insertData($logInsert);
            if(!$id ){
                throw new Exception( "保存商户资金日志数据失败" );
            }
            //处理代理信息
            if($agent_id > 0 ){
                //给代理加钱
                $agentInfo = $AgentModel->getInfo(['agent_id' => $agent_id] , true );
                $agent_now_money =  $agentInfo['money'] + $agent_real_money ;
                $sql = "update agent set money = $agent_now_money , update_time = '{$now_date}'  where agent_id = '{$agent_id}'  ";
                $update = $object->Exec($sql);
                if( !$update ){
                    throw new Exception( "修改代理商的金额出错" );
                }
                //插入代理资金日志
                $logAgentInsert = array(
                    'agent_id'=>$agent_id ,
                    'money' => $agent_real_money ,//变动的资金
                    'account_money' => $agentInfo['money'],
                    'now_money' => $agent_now_money,
                    'type'=> 5,
                    'create_time' => date("Y-m-d H:i:s" , time() ),
                    'update_time' => date("Y-m-d H:i:s" , time() ),
                    'remark' => "系统补单,商户订单号是:{$merch_order_sn}，系统订单号是:{$order_sn} 返还代理:".$agent_real_money,
                );
                $id = $AgentMoneyLogModel->insertData($logAgentInsert);
                if(!$id ){
                    throw new Exception( "保存代理资金日志数据失败" );
                }
            }
            $supplementremark ="商户补单,之前的下单人民币金额是:￥".($orderInfo['r_money']) . " ,汇率： {$orderInfo['e_rate']} 
            订单币数量：{$orderInfo['money']} 
            商户费率是:{$m_fee},之前商户到账币数量是:".($orderInfo['real_money'])
            ."之前代理的到账币数量：".($orderInfo['agent_real_money']);
            $supplementInsert = array(
                'order_sn' =>$order_sn,
                'create_date' => $now_date,
                'remark' => $supplementremark ,
            );
            $id = $OrdersSupplementsModel->insertData($supplementInsert);
            if(!$id ){
                throw new Exception( "保存到补单数据表失败" );
            }
            $object->commit();
            $Source_Merch = new Source_Merch();
            $Source_Merch->sendRsyncSupplement($order_sn , function($ret) use ($ordersModel , $order_sn) {
                $updateOrder  = [] ;
                if(strtolower($ret) == 'success' ){
                    $updateOrder = array(
                        'rsync_status' => 2 ,
                        'rsync_message'=>$ret,
                    );
                }else{
                    $updateOrder = array(
                        'rsync_status' => 3 ,
                        'rsync_message'=>$ret,
                    );
                }
                $ordersModel->updateData($updateOrder,array('order_sn' =>  $order_sn  ) );
            });
            return true ;
        }catch(Exception $e ){
            $object->rollBack();
            throw new Exception( "补单失败！！" . $e->getMessage() );
        }
    }
}