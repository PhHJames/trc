<?php
/**
 * Created by Awe.
 * User: Administrator
 * Date: 2019/10/20 0020
 * Time: 18:29
 */
class BankBusiness extends AbstractModel {
    public function getAgentBankList( $agent_id   ){
        $where = " AND a.agent_id = '{$agent_id}' ";
        $sql = "select a.*   from agent_bank as a    where  1 = 1 {$where}  order by a.create_time desc "   ;
        $bank = $this->db(0)->find( $sql);
        return $bank ;
    }
    public function addCard( $params , $agent_id  = 0  ){
        $address = isset($params['address'])?trim($params['address']):'';
        $name = isset($params['name'])?trim($params['name']):'';
        $channel = isset($params['c_name'])?trim($params['c_name']):'';
        if( empty($channel)){
            throw  new Exception("请选择币种");
        }
        if( empty($address)){
            throw  new Exception("请填写币地址");
        }
        if( empty($name)){
            throw  new Exception("请填写名称区分");
        }

        $Source_USTDTRC = new Source_USTDTRC();
        $status = $Source_USTDTRC->validateaddress($address);
        if(!$status){
            throw  new Exception("币地址不正确，请检查");
        }
        $AgentBankModel = new AgentBankModel();
        $isAllowAdd = $AgentBankModel->isAllowAddCard( $agent_id );
        if( !$isAllowAdd ){
            throw  new Exception("币地址已经超出限制不可以在添加");
        }
        $info = $AgentBankModel->getInfo(['address' =>$address]);
        if( $info ){
            throw  new Exception("币地址：{$address} 系统已经存在 ");
        }
        $insert = array(
            'agent_id' => $agent_id ,
            'name' => $name ,
            'channel' => $channel ,
            'address' => $address,
            'status' => 1 ,
            'create_time' => date("Y-m-d H:i:s" , time()),
        );
        $id = $AgentBankModel->insertData($insert);
        if(! $id ){
            throw  new Exception("币地址添加失败");
        }
        return true ;
    }

    /*public function editCard( $params ){
        $id = isset($params['id'])?trim($params['id']):'';
        $bank_type_id = isset($params['bank_type_id'])?trim($params['bank_type_id']):'';
        $card_id = isset($params['card_id'])?trim($params['card_id']):'';
        $real_name = isset($params['real_name'])?trim($params['real_name']):'';
        if( empty($id)){
            throw  new Exception("数据错误");
        }
        if( empty($bank_type_id)){
            throw  new Exception("请选择卡类型");
        }
        if( empty($card_id)){
            throw  new Exception("请填写银行卡号");
        }
        if( empty($real_name)){
            throw  new Exception("请填写卡对应的真实姓名");
        }
        if(!Tools::bank_luhm($card_id)){
            throw  new Exception("银行卡格式不正确");
        }
        $AgentBankModel = new AgentBankModel();
        $params = array(
            'real_name' => $real_name ,
            'card_id' => $card_id ,
            'bank_type_id' => intval($bank_type_id),
            'status' => 1
        );
        $res = $AgentBankModel->updateData($params,array('id'=>$id));
        if(! $res ){
            throw  new Exception("银行卡编辑失败");
        }
        return true ;
    }*/
}