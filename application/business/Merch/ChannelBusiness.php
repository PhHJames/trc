<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-04-26 22:59:32
 * @des 商户 业务文件
 */
class ChannelBusiness extends AbstractModel {
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
            $sql = "select b.fee , b.status as c_status ,b.id as c_id ,b.address  from merchants_channel_fee as b where mid = '{$params['mid']}' and channel = '{$val['channel']}'  limit 1  ";
            $info  = $this->db(0)->findOne( $sql);
            $channel[$key]['merch_channel'] = $info ;
        }
        return $channel ;
    }
    //获取商户已经开通的通道
    public function getMerchChannel( $mid ){
        $sql = "select a.fee ,   a.status as c_status  ,a.address , b.alias ,b.channel ,b.status as b_status     
        from merchants_channel_fee as a 
        left join channel as b  on a.channel = b.channel where a.mid = '{$mid}'  order by a.create_time desc     ";
        $list  = $this->db(0)->find( $sql);
        return $list ;
    }
}