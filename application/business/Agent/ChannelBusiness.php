<?php
/**
 * Created by 不要复制我的代码不然后果自负.
 * User: Administrator
 * Date: 2019/12/14 0014
 * Time: 21:53
 * @代理商的通道
 */
class ChannelBusiness extends AbstractModel {

    //获取代理下面的开的商户的所有通道
    public function getChannelList($params = array() , $agent_id  , $mid = 0 ){
        $where = "";
        if( isset($params['status'])){
            $where.= " AND a.status = '{$params['status']}' " ;
        }
        $sql = "select a.* , c.name , c.id as channel_id   from agent_channel_fee as a left join channel as c  on a.channel = c.channel    where  1 = 1 and a.agent_id = '{$agent_id}'  {$where} "   ;
        $channel = $this->db(0)->find( $sql);
        if( empty($channel )){
            return array();
        }/*echo "<pre>";
        print_r($channel);*/
        foreach ($channel as $key => $val ){
            $sql = "select b.fee , b.status as c_status ,b.id as c_id  
 from merchants_channel_fee as b  where    1 = 1  and mid = '{$mid}' and   channel = '{$val['channel']}'  limit 1  ";
            $info  = $this->db(0)->findOne( $sql);
            $channel[$key]['merch_channel'] = $info ;
        }
        return $channel ;
    }

    //获取代理商已经开通的通道
    public function getAgentChannel( $agent_id  ){
        $sql = "select a.fee ,   a.status as c_status  ,b.alias ,b.channel ,b.status as b_status      from agent_channel_fee as a 
        left join channel as b  on a.channel = b.channel where a.agent_id = '{$agent_id}'  order by a.create_time desc     ";
        $list  = $this->db(0)->find( $sql);
        return $list ;
    }
}