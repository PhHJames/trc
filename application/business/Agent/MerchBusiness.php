<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:13:56
 * @des 商户的业务逻辑文件
 */
class MerchBusiness extends AbstractModel {
    public function getMerchList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getMerchWhere($params);
        $sql = "select a.*  from merchants as a   {$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from merchants as a  {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getMerchWhere($params=array()){
        $getWhere = " AND agent_id = '{$params['agent_id']}' ";
        if(isset($params['mid']) && $params['mid']){
            $getWhere.=" and a.`mid` = '{$params['mid']}' ";
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
    public function addMerch( $params ){
        $account = $params['account'];
        $phone = $params['phone'];
        $params['password'] = md5($params['password']);
        $model = new MerchantModel();
        $info = $model->getInfo( array('account' => $account) );
        if( !empty($info) ){
            return array('code' => 0 , 'msg' => "帐号{$account}已经存在！！" );
        }
        $StringClass = new StringClass();
        $appid = $StringClass->getRandom(16 , 5  );
        $appsecret = $StringClass->getRandom( 32  , 5  );
        $params['appid'] = $appid ;
        $params['appsecret'] = $appsecret ;
        $params['status'] =  0  ;
        $params['create_time'] =   date("Y-m-d H:i:s")   ;
        $params['update_time'] =  date("Y-m-d H:i:s")  ;
        $params['agent_id'] =  $params['agent_id']; ;
        $mid  = $model->insertData($params);
        if( !$mid ){
            return array('code' => 0 , 'msg' => "添加失败请稍后" );
        }
        return array('code' => 1 , 'msg' => "添加成功");
    }
    public function editMerch( $params  , $mid ){
        $params['update_time'] =  date("Y-m-d H:i:s")  ;
        $model = new MerchantModel();
        $info = $model->getInfo( array('mid' => $mid) );
        if( empty($info) ){
            return array('code' => 0 , 'msg' => "error!!!!" );
        }
        if(!empty($params['password'])){
            $params['password'] = md5($params['password']);
        }
        $model->updateData($params,array('mid' => $mid ));
        return array('code' => 1 , 'msg' => "修改成功");
    }


    //设置商户的费率
    public function settingFee( $params = array() ,$agent_id  ){
        $cid = isset($params['cid']) ? intval($params['cid']) : 0 ;
        $mid = isset($params['mid']) ? intval($params['mid']) : 0 ;
        $status = isset($params['status']) ? intval($params['status']) : 0 ;
        $fee = isset($params['fee']) ? trim($params['fee']) : "" ;
        $channel_id = isset($params['channel_id']) ? intval($params['channel_id']) : 0 ;
        $agent_channelid = isset($params['agent_channelid']) ? intval($params['agent_channelid']) : 0 ;
        if( !in_array($status , array(1,2)) ){
            throw new Exception("状态异常");
        }
        if( empty($mid )){
            throw new Exception("商户参数错误");
        }

        $MerchantModel = new MerchantModel();
        $merchInfo = $MerchantModel->getInfo(['mid' => $mid ]);

        if( empty($merchInfo )){
            throw new Exception("商户信息错误");
        }
        if( $merchInfo['agent_id'] != $agent_id){
            throw new Exception("非法设置费率信息！！！！！");
        }
        //$fee = $fee / 100 ;
        if( $fee * 10000 < 0  ){
            Common::EchoResult(-3 , "费率错误");
        }
        if( $fee * 1000  >= 100  ){
            throw new Exception("费率错误!!");
        }
        $MerchantFeeModel = new MerchantFeeModel();
        $channelModel = new ChannelModel() ;
        $AgentChannelFeeModel = new AgentChannelFeeModel();
        $channelInfo = $channelModel->getInfo(array('id' => $channel_id ));
        if( empty($channelInfo )){
            throw new Exception("没有查询到通道");
        }
        if($channelInfo['status'] != 1  ){
            throw new Exception("通道没有开启， 无法设置费率！！");
        }
        $agentChannel = $AgentChannelFeeModel->getInfo(['id' =>$agent_channelid ]);
        if( empty( $agentChannel )){
            throw new Exception("代理的通道没有查询到");
        }
        if($agentChannel['status'] != 1  ){
            throw new Exception("代理的通道没有开启， 无法设置费率！！");
        }
        /*echo $agentChannel['fee'] * 1000  ;
        echo "<br>";
        echo $fee * 1000 ;
        die();*/
        if( $channelInfo['fee']   >=  $fee  ){
            //throw new Exception("费率设置必须大于系统的：{$channelInfo['fee']}");
        }
        if( $agentChannel['fee']   >  $fee  ){
            throw new Exception("费率--设置必须大于：{$agentChannel['fee']}");
        }
        $feeData = $MerchantFeeModel->getInfo( array('id' => $cid ) );
        if( !empty($feeData )){
            //修改
            $MerchantFeeModel->updateData( array('fee' => $fee , 'status' => $status ) , array('id' => $cid ) );
        }else{
            $insertData = array(
                'channel' =>$channelInfo['channel'],
                'mid' => $mid ,
                'fee' => $fee ,
                'create_time' => date("Y-m-d H:i:s"),
                'update_time' => date("Y-m-d H:i:s"),
                'status' => $status
            );
            $MerchantFeeModel->insertData($insertData);
        }
        return true ;
    }
}