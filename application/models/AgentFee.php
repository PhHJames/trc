<?php
/**
 * Created by 不要复制我的代码不然后果自负.
 * User: 不要复制我的代码不然后果自负
 * Date: 2019/5/14 0014
 * Time: 上午 11:45
 */
class AgentFeeModel extends AbstractModel{
    protected $table = "agent_channel_fee" ;
    protected $dbIndex = 0  ;
    //获取代理商已经开通的费率
    public function getMerchFee($agent_id){
        $sql = "select a.* , b.alias,b.min_money,b.max_money from {$this->table} as a left join channel as b on a.channel = b.channel where a.agent_id = '{$agent_id}'
 and a.status = '1' and b.status = 1   ";
        return $this->db($this->dbIndex)->find( $sql);
    }

    //根据代理商的id 和 通道类型 获取费率
    public function getAgentHasChannel($agent_id , $channel ){
        $sql = "select a.*  from {$this->table} as a  where a.agent_id = '{$agent_id}' and channel = '{$channel}'  limit 1 ";
        return $this->db($this->dbIndex)->findOne( $sql);
    }

}