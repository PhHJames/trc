<?php
/**
 * Created by Awe.
 * User: Administrator
 * Date: 2019/10/20 0020
 * Time: 18:29
 */
class BankBusiness extends AbstractModel {
    public function getMerchBankList( $mid  ){
        $where = " AND a.mid = '{$mid}' ";
        $sql = "select a.*   from merchants_bank as a   where  1 = 1 {$where}  order by a.create_time desc "   ;
        $bank = $this->db(0)->find( $sql);
        return $bank ;
    }
    public function addCard( $params , $mid ){
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
        $MerchantsBankModel = new MerchantsBankModel();
        $isAllowAdd = $MerchantsBankModel->isAllowAddCard( $mid );
        if( !$isAllowAdd ){
            throw  new Exception("币地址已经超出限制不可以在添加");
        }
        $info = $MerchantsBankModel->getInfo(['address' =>$address]);
        if( $info ){
            throw  new Exception("币地址：{$address} 系统已经存在 ");
        }
        $insert = array(
            'mid' => $mid ,
            'name' => $name ,
            'channel' => $channel ,
            'address' => $address,
            'status' => 1 ,
            'create_time' => date("Y-m-d H:i:s" , time()),
        );
        $id = $MerchantsBankModel->insertData($insert);
        if(! $id ){
            throw  new Exception("币地址添加失败");
        }
        return true ;
    }
}