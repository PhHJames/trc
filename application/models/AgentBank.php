<?php
/**
 * Created by 不要复制我的代码不然后果自负 .
 * User: Administrator
 * Date: 2019/10/20 0020
 * Time: 18:23
 */

class AgentBankModel extends AbstractModel{
    protected $table = "agent_bank" ;
    protected $dbIndex = 0  ;
    //查询用户已经存在的银行卡
    public function getHasExistsCard( $agent_id ){
        $sql = "select * from " . $this->table . " where status in (1 , 2 ) and agent_id = '{$agent_id}'  " ;
        return $this->db($this->dbIndex)->find( $sql );
    }

    public function isAllowAddCard($agent_id){
        $res = $this->getHasExistsCard($agent_id);
        $AgentModel = new AgentModel();
        $agentInfo = $AgentModel->getInfo(['agent_id' => $agent_id]);
        $count_config =  isset($agentInfo['max_address_count'])?$agentInfo['max_address_count']:0;
        if( count($res) >= $count_config){
            return false;
        }
        return true ;
    }
    //获取代理商审核通过的银行卡
    public function getAvalialeCard($agent_id){
        $sql = "select * from " . $this->table . " where status in (2 ) and mid = '{$agent_id}'  " ;
        return $this->db($this->dbIndex)->find( $sql );
    }
}