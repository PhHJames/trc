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
        $sql = "select a.*,b.name as w_name  from agent_withdraw as a left join agent_bank as b 
  
        on a.bank_id = b.id  {$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from agent_withdraw as a  left join agent_bank as b 
  
        on a.bank_id = b.id  {$getWhere} " ;
        $data =  $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
        $list = $data['list'];
        $total = $data['total'];
        return array('list' => $list , 'total' => $total );
    }

    public function getWhere($params=array()){
        $getWhere = " AND a.agent_id  = '{$params['agent_id']}' ";
        if(isset($params['order_no']) && $params['order_no']){
            $getWhere.=" and a.`order_no` = '{$params['order_no']}' ";
        }
        if(isset($params['status']) && $params['status']){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        if(isset($params['mid']) && $params['mid']){
            $getWhere.=" and a.`mid` = '{$params['mid']}' ";
        }
        return $getWhere;
    }
    public function doWithdraw( $params , $agent_id  ){
        $bank_id = isset($params['bank_id'])?trim($params['bank_id']):'';
        $money = isset($params['money'])?trim($params['money']):'';
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
        $AgentModel = new AgentModel();
        $AgentBankModel = new AgentBankModel();
        $AgentWithDrawModel = new AgentWithDrawModel();
        $AgentMoneyLogModel = new AgentMoneyLogModel();
        $agentInfo = $AgentModel->getInfo( array('agent_id' => $agent_id ) , true  );

        $min_withdraw_money = trim($agentInfo['min_withdraw_money']);//最低提现金额
        if( $fen < $min_withdraw_money * pow(10 , NUMBER_FORMAT) ){
            throw new Exception( "最低提现金额必须大于 {$min_withdraw_money}" );
        }
        $bankInfo = $AgentBankModel->getInfo( array('id' =>$bank_id , 'agent_id' => $agent_id  ) );
        if( empty($bankInfo ) ){
            throw new Exception( "没有查询到相关的币地址信息" );
        }
        if($bankInfo['status'] != 2 ){
            throw new Exception( "你选择的币没有审核通过" );
        }
        $object = $this->db(0);
        try{
            $object->beginTransaction();
            $agentInfo = $AgentModel->getInfo( array('agent_id' => $agent_id ) , true  );
            if( empty($agentInfo ) ){
                throw new Exception( "没有查询到代理的信息" );
            }
            if($agentInfo['status'] != 1 ){
                throw new Exception( "商户暂时不允许提现，登录账号异常" );
            }
            if( $agentInfo['money'] <= 0 ){
                throw new Exception( "账户币地址余额不足" );
            }
            //需要从代理账户里面扣除的钱为
            $shouxufei = $agentInfo['fixed_poundage']  ; //提现的手续费
            $need_money = $money + $shouxufei ;
            /*echo $need_money ;
            echo "<h1>";
            echo $merchInfo['money'];
            die();*/
            if( $need_money > $agentInfo['money'] ){
                throw new Exception( "账户币不足!!!" );
            }
            $subtract_money = $money - $agentInfo['fixed_poundage'] ;
            if($subtract_money <= 0 ){
                //throw new Exception( "提现币数量小于等于手续费" );
            }
            $withDraw = array(
                'agent_id' => $agent_id ,
                'money' => $money ,
                'create_time' => date("Y-m-d H:i:s" , time() ),
                'update_time' => date("Y-m-d H:i:s" , time() ),
                'fixed_poundage' => $agentInfo['fixed_poundage'],
                'status' => 1 ,
                'order_no' => Tools::build_order_no(32),
                'bank_id'=> $bankInfo['id']
            );
            $id = $AgentWithDrawModel->insertData($withDraw);
            if(!$id ){
                throw new Exception( "写入提现数据失败" );
            }
            //更改代理的的钱
            $now_money =  $agentInfo['money'] - $need_money ;
            $now_freez_money = $agentInfo['freez_money'] + $need_money ;
            $sql = "update agent set money = $now_money ,freez_money = '{$now_freez_money}'  where agent_id = '{$agent_id}' ";
            $update = $object->Exec($sql);
            if( !$update ){
                throw new Exception( "修改商户的金额出错" );
            }
            //插入资金日志
            $logInsert = array(
                'agent_id'=>$agent_id ,
                'money' => -$need_money ,
                'account_money' => $agentInfo['money'],
                'now_money' => $now_money,
                'type'=> 1,
                'create_time' => date("Y-m-d H:i:s" , time() ),
                'update_time' => date("Y-m-d H:i:s" , time() ),
                'remark' => "商户提现手续费:".($shouxufei),
            );
            $id = $AgentMoneyLogModel->insertData($logInsert);
            if(!$id ){
                throw new Exception( "保存代理资金日志数据失败" );
            }
            $object->commit();
            return true ;
        }catch(Exception $e ){
            $object->rollBack();
            throw new Exception( "代理提现失败！！" . $e->getMessage() );
        }
    }

    //获取商户允许使用提现的银行卡
    public function getAgentWithdrawCard( $agent_id  ){
        $sql = "select a.*  from agent_bank as a  where a.status in ( 2 ) and a.agent_id = '{$agent_id}'  " ;
        return $this->db( 0 )->find( $sql );
    }
}