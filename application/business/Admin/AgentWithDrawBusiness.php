<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:13:56
 * @des 代理商的业务逻辑文件
 */
class AgentWithDrawBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.* , b.name ,b.agent_id ,b.account ,c.name as w_name  , c.address   from agent_withdraw as a left join  agent as b on a.agent_id = b.agent_id 
        left join agent_bank as c on c.id = a.bank_id 
{$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from agent_withdraw as a left join  agent as b on a.agent_id = b.agent_id   left join agent_bank as c on c.id = a.bank_id 
       {$getWhere} " ;
        $data =  $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
        $list = $data['list'];
        $total = $data['total'];
        //$this->buildList($list);
        return array('list' => $list , 'total' => $total );
    }

    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['agent_id']) && $params['agent_id']){
            $getWhere.=" and b.`agent_id` = '{$params['agent_id']}' ";
        }
        if(isset($params['account']) && $params['account']){
            $getWhere.=" and b.`account` = '{$params['account']}' ";
        }
        if(isset($params['status']) AND  $params['status'] ){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        return $getWhere;
    }

   public function auth( $params ){
       $id = isset($params['id'])?intval($params['id']):'';
       $remark = isset($params['remark'])?trim($params['remark']):'';
       $status = isset($params['status'])?trim($params['status']):'';
       $AgentWithDrawModel = new AgentWithDrawModel();
       $AgentModel = new AgentModel();
       $AgentMoneyLogModel =  new AgentMoneyLogModel();
       if( !in_array($status , array(2 , 3 ) )){
           throw new Exception("非法访问。。。。。。");
       }
       $Source_USTDTRC = new Source_USTDTRC();
       $object = $this->db(0);
       try{
           $object->beginTransaction();
           $info  = $AgentWithDrawModel->getInfo( array('id' => $id ), true  );
           if( empty($info ) ){
               throw new Exception( "暂无数据" );
           }
           if( $info['status'] != 1 ){
               throw new Exception( "只有待审核的提现才能操作" );
           }
           $agent_id = $info['agent_id'];
           //查询代理商的表
           $agent_info  = $AgentModel->getInfo(array('agent_id' => $info['agent_id']) , true  );
           if( empty($agent_info) ){
               throw new Exception( "没有查询到代理商的信息" );
           }
           $update = array(
               'status' => $status ,
               'remark' => $remark ,
               'update_time' => date("Y-m-d H:i:s" , time() ),
           );

           if($status == 3 ){
               if( empty($info['txid'] )){
                   throw new Exception( "无交易id， 没有进行第一步确认" );
               }

               $is_pay = $Source_USTDTRC->checkTransIsSuccess( $info['txid'] );
               if(!$is_pay){
                   throw new Exception( "系统没有查询到付款成功 请再三确认下" );
               }
               //审核通过
               $money = $info['money'] + $info['fixed_poundage'] ;//准备给商户返回多少钱
               $now_freez_money = $agent_info['freez_money']  -  $money ;
               $sql = "update agent set `freez_money` = $now_freez_money  where agent_id = '{$agent_id}' ";
               $updateStat = $object->Exec($sql);
               if( !$updateStat ){
                   throw new Exception( "修改代理的金额出错" );
               }
           }


           $updateStatus = $AgentWithDrawModel->updateData($update,array('id' => $id ) );
           if( !$updateStatus ){
               throw new Exception( "修改提现数据失败" );
           }
           if($status == 2 ){
               $money = $info['money'] + $info['fixed_poundage'] ;//准备给代理商返回多少钱
               //如果是驳回 那么 把钱给代理商返回去
               $now_money =  $agent_info['money'] + $money ;
               $now_freez_money = $agent_info['freez_money']  -  $money ;
               $sql = "update agent set money = $now_money , freez_money = $now_freez_money  where agent_id = '{$agent_id}' ";
               $update = $object->Exec($sql);
               if( !$update ){
                   throw new Exception( "修改代理商的币出错" );
               }
               //插入资金日志
               $logInsert = array(
                   'agent_id'=>$agent_id ,
                   'money' => $money ,
                   'account_money' => $agent_info['money'],
                   'now_money' => $now_money,
                   'type'=> 2,
                   'create_time' => date("Y-m-d H:i:s" , time() ),
                   'update_time' => date("Y-m-d H:i:s" , time() ),
                   'remark' => "审核提现驳回 返还代理商:".($money),
               );
               $id = $AgentMoneyLogModel->insertData($logInsert);
               if(!$id ){
                   throw new Exception( "保存代理商资金日志数据失败" );
               }
           }
           $object->commit();
           return true ;
       }catch(Exception $e ){
           $object->rollBack();
           throw new Exception( "审核代理商提现！！" . $e->getMessage() );
       }
   }

   public function withdrawNotDo(){
        $sql = "select * from agent_withdraw where status = 1 limit 1 ";
        return $this->db(0)->findOne($sql);
   }


    public function trans_usdt( $params = [] ){
        $id = isset($params['id']) ? $params['id'] :"";
        $from_address = isset($params['address']) ? $params['address'] :"";
        $remark= isset($params['remark']) ? $params['remark'] :"";
        $AgentWithDrawModel = new AgentWithDrawModel();
        $info = $AgentWithDrawModel->getInfo(['id' => $id ]);
        $AgentBankModel = new AgentBankModel();
        $bank_info = $AgentBankModel->getInfo(['id' => $info['bank_id'] ]);
        if( empty($info) or empty($bank_info )){
            throw new Exception( "参数错误 没有查询到数据" );
        }
        $AddressTransCommonBusiness = Common::ImportBusiness("AddressTransCommon" ,"Common");
        $to_address = $bank_info['address'];
        $Source_Account = new Source_Account();
        $SummaryAccount = $Source_Account->getSummaryAccountByAddress($from_address);
        if( empty($SummaryAccount)){
            throw new Exception( "没有目标账号" );
        }
        $private_key = isset($SummaryAccount['private_key']) ? $SummaryAccount['private_key'] :"";
        if( empty($private_key) ){
            throw new \Exception("没有目标账号的私钥");
        }

        $amount = $info['money'];

        //$amount = 0.001 ;
        //echo $amount;die();
        $s_remark = "给代理打款源账号：{$from_address} ($remark)"  ;
        $res = $AddressTransCommonBusiness->trc20_trans($private_key , $from_address  , $to_address , $amount,$s_remark);
        $txid = $res['txid'];
        if( empty($txid) ){
            throw new \Exception("打款失败 没有获取到交易的id，请去网站上确认不要重复打款");
        }
        $AgentWithDrawModel->updateData( ['txid' => $txid , 'remark' => $s_remark  ] , ['id' => $id ] );
        return true ;
    }


}