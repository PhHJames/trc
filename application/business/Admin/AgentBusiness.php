<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:13:56
 * @des  代理商的业务逻辑文件
 */
class AgentBusiness extends AbstractModel {
    public function getAgentList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getAgentWhere($params);
        $sql = "select a.*  from agent as a   {$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from merchants as a  {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getAgentWhere($params=array()){
        $getWhere = "";
        if(isset($params['agent_id']) && $params['agent_id']){
            $getWhere.=" and a.`agent_id` = '{$params['agent_id']}' ";
        }
        if(isset($params['account']) && $params['account']){
            $getWhere.=" and a.`account` = '{$params['account']}' ";
        }
        if(isset($params['phone']) && $params['phone']){
            $getWhere.=" and a.`phone` = '{$params['phone']}' ";
        }
        if(isset($params['status']) AND  $params['status'] != 99){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        return $getWhere;
    }
    //添加代理商
    public function addAgent( $params ){
        $account = $params['account'];
        $phone = $params['phone'];
        $params['password'] = md5($params['password']);
        $model = new AgentModel();
        $info = $model->getInfo( array('account' => $account) );
        if( !empty($info) ){
            return array('code' => 0 , 'msg' => "帐号{$account}已经存在！！" );
        }
        $params['status'] =  0  ;
        $params['create_time'] =   date("Y-m-d H:i:s")   ;
        $params['update_time'] =  date("Y-m-d H:i:s")  ;
        $mid  = $model->insertData($params);
        if( !$mid ){
            return array('code' => 0 , 'msg' => "添加失败请稍后" );
        }
        return array('code' => 1 , 'msg' => "添加成功");
    }
    public function editAgent( $params  , $agent_id ){
        $params['update_time'] =  date("Y-m-d H:i:s")  ;
        $model = new AgentModel();
        $info = $model->getInfo( array('agent_id' => $agent_id) );
        if( empty($info) ){
            return array('code' => 0 , 'msg' => "error!!!!" );
        }
        if(!empty($params['password'])){
            $params['password'] = md5($params['password']);
        }
        $model->updateData($params,array('agent_id' => $agent_id ));
        return array('code' => 1 , 'msg' => "修改成功");
    }


    //设置代理商的费率
    public function settingFee( $params = array()  ){
        $cid = isset($params['cid']) ? intval($params['cid']) : 0 ;
        $agent_id = isset($params['agent_id']) ? intval($params['agent_id']) : 0 ;
        $status = isset($params['status']) ? intval($params['status']) : 0 ;
        $fee = isset($params['fee']) ? trim($params['fee']) : "" ;
        $channel_id = isset($params['channel_id']) ? intval($params['channel_id']) : 0 ;
        if( !in_array($status , array(1,2)) ){
            throw new Exception("状态异常");
        }
        if( empty($agent_id )){
            throw new Exception("代理商参数错误");
        }

        //$fee = $fee / 100 ;
        if( $fee * 10000 < 0  ){
            Common::EchoResult(-3 , "费率错误,必须大于等于0");
        }
        if( $fee * 1000  >= 100  ){
            Common::EchoResult(-3 , "费率错误!!");
        }
        $AgentFeeModel = new AgentFeeModel();
        $channelModel = new ChannelModel() ;
        $channelInfo = $channelModel->getInfo(array('id' => $channel_id ));
        if( empty($channelInfo )){
            throw new Exception("没有查询到通道");
        }
        if($channelInfo['status'] != 1  ){
            throw new Exception("通道没有开启，无法设置费率！！");
        }
        if($channelInfo['fee'] > $fee){
            //throw new Exception("不能低于系统该通道费率".$channelInfo['fee']);
        }
        $feeData = $AgentFeeModel->getInfo( array('id' => $cid ) );
        if( !empty($feeData )){
            //修改
            $AgentFeeModel->updateData( array('fee' => $fee , 'status' => $status ) , array('id' => $cid ) );
        }else{
            $insertData = array(
                'channel' =>$channelInfo['channel'],
                'agent_id' => $agent_id ,
                'fee' => $fee ,
                'create_time' => date("Y-m-d H:i:s"),
                'update_time' => date("Y-m-d H:i:s"),
                'status' => $status
            );
            $AgentFeeModel->insertData($insertData);
        }
        return true ;
    }

    public function doMoney( $params = array()  ){
        $agent_id = isset($params['agent_id']) ? intval($params['agent_id']) : 0 ;
        $type = isset($params['type']) ? intval($params['type']) : 0 ;
        $money = isset($params['money']) ? trim($params['money']) : "" ;
        if( !in_array($type , array(1,2)) ){
            throw new Exception("状态异常");
        }
        if( empty($agent_id )){
            throw new Exception("代理商参数错误");
        }
        if( empty($money )){
            throw new Exception("请输入操作金额");
        }
        $money = floatval($money);
        if( $money * 100 <= 0  ){
            Common::EchoResult(-3 , "金额错误");
        }
        $AgentModel = new AgentModel();
        $AgentMoneyLogModel = new AgentMoneyLogModel();
        $object = $this->db(0);
        try{
            $object->beginTransaction();
            //查询代理商的表
            $agentInfo = $AgentModel->getInfo(array('agent_id' => $agent_id ) , true  );
            if( empty($agentInfo) ){
                throw new Exception( "没有查询到代理商的信息" );
            }

            $now_money =  0 ;
            $change_money = 0 ;//变动的金额
            if( $type == 1 ){//添加
                $now_money =  intval($agentInfo['money'] + $money * 100) ;
                $change_money = $money * 100 ;
            }else if( $type == 2 ){
                $now_money =  intval($agentInfo['money']  -  $money * 100) ;
                $change_money = -$money * 100 ;
            }
            if($now_money < 0 ){
                throw new Exception( "计算金额错误" );
            }
            $update = array(
                'money' => $now_money ,
                'update_time' => date("Y-m-d H:i:s" , time() ),
            );
            $updateMerch = $AgentModel->updateData($update,array('agent_id' => $agent_id ) );
            if( !$updateMerch ){
                throw new Exception( "修改代理商数据失败" );
            }
            //插入资金日志
            $logInsert = array(
                'agent_id'=> $agent_id ,
                'money' => $change_money ,
                'account_money' => $agentInfo['money'],
                'now_money' => $now_money,
                'type'=> 4,
                'create_time' => date("Y-m-d H:i:s" , time() ),
                'update_time' => date("Y-m-d H:i:s" , time() ),
                'remark' => "平台操作代理商金额,变动: ".($change_money/100),
            );
            $id = $AgentMoneyLogModel->insertData($logInsert);
            if(!$id ){
                throw new Exception( "保存代理商资金日志数据失败" );
            }
            $object->commit();
            return true ;
        }catch(Exception $e ){
            $object->rollBack();
            throw new Exception( "操作代理商资金日志！！" . $e->getMessage() );
        }
    }



    public function getChannelList($params = array() ){
        $where = "";
        if( isset($params['status'])){
            $where.= " AND a.status = '{$params['status']}' " ;
        }
        $sql = "select a.*  from channel as a    where  1 = 1 {$where} "   ;
        $channel = $this->db(0)->find( $sql);
        if( empty($channel )){
            return array();
        }/*echo "<pre>";
        print_r($channel);*/
        foreach ($channel as $key => $val ){
            $sql = "select b.fee , b.status as c_status ,b.id as c_id   from agent_channel_fee as b where agent_id = '{$params['agent_id']}' and channel = '{$val['channel']}'  limit 1  ";
            $info  = $this->db(0)->findOne( $sql);
            $channel[$key]['agent_channel'] = $info ;
        }
        return $channel ;
    }
    //获取代理商已经开通的通道
    public function getAgentChannel( $agent_id ){
        $sql = "select a.fee ,   a.status as c_status  ,b.alias ,b.channel ,b.status as b_status      from agent_channel_fee as a 
        left join channel as b  on a.channel = b.channel where a.agent_id = '{$agent_id}'  order by a.create_time desc     ";
        $list  = $this->db(0)->find( $sql);
        return $list ;
    }


}