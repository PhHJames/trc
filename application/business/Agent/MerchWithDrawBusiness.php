<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:13:56
 * @des 商户的业务逻辑文件
 */
class MerchWithDrawBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize, $agent_id ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select y.* from (select a.* , b.name ,b.account ,b.agent_id  from merchants_withdraw as a left join  merchants as b on a.mid = b.mid 
{$getWhere} order by a.create_time desc) as y where y.agent_id = {$agent_id}"   ;
        $sql_count = "select count(*) as c  from (select a.* ,b.agent_id  from merchants_withdraw as a left join  merchants as b on a.mid = b.mid 
{$getWhere} ) as y where y.agent_id = {$agent_id}" ;
        $data =  $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
        $list = $data['list'];
        $total = $data['total'];
        $this->buildList($list);
        return array('list' => $list , 'total' => $total );
    }
    private function buildList( &$list ){
        if( empty($list )){
            return array();
        }
        $bank_type_id_array = [];
        foreach ($list as $key => $val ){
            $bank_info = json_decode($val['bank_info'],true ) ;
            $card_id = isset($bank_info['card_id']) ?$bank_info['card_id'] : "" ;
            $real_name = isset($bank_info['real_name']) ?$bank_info['real_name'] : "" ;
            $bank_type_id = isset($bank_info['bank_type_id']) ?$bank_info['bank_type_id'] : "" ;
            array_push($bank_type_id_array , $bank_type_id);
            $list[$key]['bank_type_id'] = $bank_type_id;
            $list[$key]['card_id'] = $card_id;
            $list[$key]['real_name'] = $real_name;
        }
        $ids = implode( "," ,$bank_type_id_array );
        $sql = "select * from bank_type where id in ($ids) ";
        $res = $this->db(0)->find($sql);
        if( empty($res) ){
            return array();
        }
        $hash = [] ;
        foreach ($res as $r => $rval ){
            $hash[$rval['id']] = $rval ;
        }
        foreach ($list as $skey => $sval ){
            $bank_name = isset($hash[$sval['bank_type_id']]['bank_name'])?$hash[$sval['bank_type_id']]['bank_name']:"";
            $list[$skey]['bank_name'] = $bank_name;
        }
    }
    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['mid']) && $params['mid']){
            $getWhere.=" and b.`mid` = '{$params['mid']}' ";
        }
        if(isset($params['account']) && $params['account']){
            $getWhere.=" and b.`account` = '{$params['account']}' ";
        }
        if(isset($params['status']) AND  $params['status'] ){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        return $getWhere;
    }

}