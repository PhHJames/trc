<?php
/**
 * Created by 不要复制我的代码不然后果自负.
 * User: 不要复制我的代码不然后果自负
 * Date: 2019/5/14 0014
 * Time: 上午 11:45
 */
class AgentChannelFeeModel extends AbstractModel{
    protected $table = "agent_channel_fee" ;
    protected $dbIndex = 0  ;
    //查询代理商的最大费率
    public function getMaxFee($channel = '' ){
        $sql = "select max(fee) as fee from agent_channel_fee where channel = '{$channel}' limit 1 ";
        $info = $this->db($this->dbIndex)->find($sql);
        return isset($info[0]['fee']) ? $info[0]['fee'] : 0 ;
    }
}