<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 * @des 商户的业务逻辑文件
 */
class MerchBusiness extends AbstractModel {
    public function getMerchList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getMerchWhere($params);
        $sql = "select a.* , b.name as agent_name   from merchants as a  left join agent as b on a.agent_id = b.agent_id  {$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from merchants as a  left join agent as b on a.agent_id = b.agent_id {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getMerchWhere($params=array()){
        $getWhere = "";
        if(isset($params['mid']) && $params['mid']){
            $getWhere.=" and a.`mid` = '{$params['mid']}' ";
        }
        if(isset($params['account']) && $params['account']){
            $getWhere.=" and a.`account` = '{$params['account']}' ";
        }
        if(isset($params['status']) AND  $params['status'] != 99){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        return $getWhere;
    }

    public function getAjaxBindcollectuserList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getBindcollectWhere($params);
        $sql = "select a.*  from collect_user as a   {$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from collect_user as a  {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getBindcollectWhere($params=array()){
        $getWhere = "";
        if(isset($params['account']) && $params['account']){
            $getWhere.=" and a.`account` = '{$params['account']}' ";
        }
        if(isset($params['cid']) && $params['cid']){
            $params['cid']  = intval($params['cid']);
            $getWhere.=" and a.`user_id` = '{$params['cid']}' ";
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
    public function settingFee( $params = array()  ){
        throw new Exception("暂不支持在后台修改商户的费率，请在代理商的后台进行修改");
        $cid = isset($params['cid']) ? intval($params['cid']) : 0 ;
        $mid = isset($params['mid']) ? intval($params['mid']) : 0 ;
        $status = isset($params['status']) ? intval($params['status']) : 0 ;
        $fee = isset($params['fee']) ? trim($params['fee']) : "" ;
        $address = isset($params['address']) ? trim($params['address']) : "" ;
        $channel_id = isset($params['channel_id']) ? intval($params['channel_id']) : 0 ;
        if( !in_array($status , array(1,2)) ){
            throw new Exception("状态异常");
        }
        if( empty($mid )){
            throw new Exception("商户参数错误");
        }

        //$fee = $fee / 100 ;
        if( $fee * 10000 <= 0  ){
            Common::EchoResult(-3 , "费率错误");
        }
        if( $fee * 1000  >= 100  ){
            Common::EchoResult(-3 , "费率错误!!");
        }
        $MerchantFeeModel = new MerchantFeeModel();
        $channelModel = new ChannelModel() ;
        $channelInfo = $channelModel->getInfo(array('id' => $channel_id ));
        if( empty($channelInfo )){
            throw new Exception("没有查询到通道");
        }
        /*if( empty($address )){
            throw new Exception("收款地址必须配置");
        }
        $Source_USTDTRC = new Source_USTDTRC();
        $is_valiate = $Source_USTDTRC->validateaddress( $address );
        if( !$is_valiate){
            throw new Exception("收款地址不正确， 请核实 ");
        }*/
        $feeData = $MerchantFeeModel->getInfo( array('id' => $cid ) );
        if( !empty($feeData )){
            //修改
            $MerchantFeeModel->updateData( array('fee' => $fee , 'status' => $status  , 'address' => $address) , array('id' => $cid ) );
        }else{
            $insertData = array(
                'channel' =>$channelInfo['channel'],
                'mid' => $mid ,
                'fee' => $fee ,
                'create_time' => date("Y-m-d H:i:s"),
                'update_time' => date("Y-m-d H:i:s"),
                'status' => $status,
                'address' => $address
            );
            $MerchantFeeModel->insertData($insertData);
        }
        return true ;
    }

    //资金操作
    public function doMoney( $params = array()  ){
        $mid = isset($params['mid']) ? intval($params['mid']) : 0 ;
        $type = isset($params['type']) ? intval($params['type']) : 0 ;
        $money = isset($params['money']) ? trim($params['money']) : "" ;
        $new_remark = isset($params['remark']) ? trim($params['remark']) : "" ;
        if( !in_array($type , array(1,2)) ){
            throw new Exception("状态异常");
        }
        if( empty($mid )){
            throw new Exception("商户参数错误");
        }
        if( empty($money )){
            throw new Exception("请输入操作金额");
        }
        $money = floatval($money);
        if( $money * NUMBER_FORMAT <= 0  ){
            Common::EchoResult(-3 , "金额错误");
        }
        $MerchantModel = new MerchantModel();
        $MerchantMoneyLogModel = new MerchantMoneyLogModel();
        $object = $this->db(0);
        try{
            $object->beginTransaction();
            //查询商户的表
            $merchInfo = $MerchantModel->getInfo(array('mid' => $mid ) , true  );
            if( empty($merchInfo) ){
                throw new Exception( "没有查询到商户的信息" );
            }

            $now_money =  0 ;
            $change_money = 0 ;//变动的金额
            if( $type == 1 ){//添加
                $now_money =  Tools::number_format( ($merchInfo['money'] + $money ) , NUMBER_FORMAT  ) ;
                $change_money = $money  ;
            }else if( $type == 2 ){
                $now_money =  Tools::number_format(($merchInfo['money']  -  $money ) , NUMBER_FORMAT  ) ;
                $change_money = -$money  ;
            }
            if($now_money < 0 ){
                throw new Exception( "计算金额错误,最终计算出的账户资金不能小于0 " );
            }
            $update = array(
                'money' => $now_money ,
                'update_time' => date("Y-m-d H:i:s" , time() ),
            );
            $updateMerch = $MerchantModel->updateData($update,array('mid' => $mid ) );
            if( !$updateMerch ){
                throw new Exception( "修改商户数据失败" );
            }
            //插入资金日志
            $logInsert = array(
                'mid'=> $mid ,
                'money' => $change_money ,
                'account_money' => $merchInfo['money'],
                'now_money' => $now_money,
                'type'=> 4,
                'create_time' => date("Y-m-d H:i:s" , time() ),
                'update_time' => date("Y-m-d H:i:s" , time() ),
                'remark' => "平台操作商户金额,变动: ".($change_money) . "(" . $new_remark . ")" ,
            );
            $id = $MerchantMoneyLogModel->insertData($logInsert);
            if(!$id ){
                throw new Exception( "保存商户资金日志数据失败" );
            }
            $object->commit();
            return true ;
        }catch(Exception $e ){
            $object->rollBack();
            throw new Exception( "操作商户资金日志！！" . $e->getMessage() );
        }
    }

    public function doBindCollectUser($params = array()){
        $mid = isset($params['mid']) ? intval($params['mid']) : 0 ;
        $user_id = isset($params['user_id']) ? intval($params['user_id']) : "" ;
        $MerchantsBindCollectModel = new MerchantsBindCollectModel();
        $bindDataUser = $MerchantsBindCollectModel->getInfo(['user_id' => $user_id]);
        if( empty($user_id )){
            throw new Exception("缺失用户参数");
        }
        if( empty($mid )){
            throw new Exception("缺失商户参数");
        }
        if( !empty($bindDataUser )){
            throw new Exception("用户已经绑定");
        }

        $insert = [
            'mid' => $mid ,
            'user_id' => $user_id ,
        ];
        $MerchantsBindCollectModel->insertData($insert);
        return true ;
    }
    public function doDeleteBindCollectUser($params = array()){
        $mid = isset($params['mid']) ? intval($params['mid']) : 0 ;
        $user_id = isset($params['user_id']) ? intval($params['user_id']) : "" ;
        $MerchantsBindCollectModel = new MerchantsBindCollectModel();
        $bindDataUser = $MerchantsBindCollectModel->getInfo(['user_id' => $user_id]);
        if( empty($user_id )){
            throw new Exception("缺失用户参数");
        }
        if( empty($mid )){
            throw new Exception("缺失商户参数");
        }
        if( empty($bindDataUser )){
            throw new Exception("暂无绑定不能删除");
        }
        $sql = "delete from merchants_bind_collect where mid = {$mid} and user_id = '{$user_id}' ";
        $this->db(0)->Exec($sql);
        return true ;
    }

}