<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last Modified by:   Awe
 * @Last Modified time: 2018-11-08 11:13:56
 * @des 商户的业务逻辑文件
 */
class AgentBankBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getWhere($params);
        $sql = "select a.* , b.name ,b.agent_id ,b.account   from agent_bank as a left join  agent as b on a.agent_id = b.agent_id
{$getWhere} order by a.create_time desc "   ;

        $sql_count = "select count(*) as c   from agent_bank as a left join  agent as b on a.agent_id = b.agent_id   {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getWhere($params=array()){
        $getWhere = "";
        if(isset($params['agent_id']) && $params['agent_id']){
            $getWhere.=" and b.`agent_id` = '{$params['agent_id']}' ";
        }
        if(isset($params['account']) && $params['account']){
            $getWhere.=" and b.`account` = '{$params['account']}' ";
        }
        if(isset($params['status']) AND  $params['status'] ){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        return $getWhere;
    }

   public function doManager( $params ){
       $id = isset($params['id'])?intval($params['id']):'';
       $remark = isset($params['remark'])?trim($params['remark']):'';
       $status = isset($params['status'])?trim($params['status']):'';
       $AgentBankModel = new AgentBankModel();
       $info  = $AgentBankModel->getInfo( array('id' => $id ) );
       if( empty($info )){
           throw new Exception("暂无数据");
       }
       $update = array(
           'status' => $status ,
           'remark' => $remark
       );
       $AgentBankModel->updateData($update , array('id' => $id ) ) ;
       return true ;
   }

}