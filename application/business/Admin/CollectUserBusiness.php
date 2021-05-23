<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 */
class CollectUserBusiness extends AbstractModel {
    public function getCollectUserList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getCollectWhere($params);
        $sql = "select a.*  from collect_user as a   {$getWhere} order by a.create_time desc "   ;
        $sql_count = "select count(*) as c   from collect_user as a  {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getCollectWhere($params=array()){
        $getWhere = "";
        if(isset($params['user_id']) && $params['user_id']){
            $getWhere.=" and a.`user_id` = '{$params['user_id']}' ";
        }
        if(isset($params['account']) && $params['account']){
            $getWhere.=" and a.`account` = '{$params['account']}' ";
        }
        if(isset($params['status']) AND  $params['status'] != 99){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        return $getWhere;
    }
    public function addCollstUser( $params ){
        $account = $params['account'];
        $phone = $params['phone'];
        $params['password'] = md5($params['password']);
        $model = new CollectUserModel();
        $info = $model->getInfo( array('account' => $account) );
        if( !empty($info) ){
            return array('code' => 0 , 'msg' => "帐号{$account}已经存在！！" );
        }
        $StringClass = new StringClass();
        $params['status'] =  1  ;
        $params['create_time'] =   date("Y-m-d H:i:s")   ;
        $params['update_time'] =  date("Y-m-d H:i:s")  ;
        $mid  = $model->insertData($params);
        if( !$mid ){
            return array('code' => 0 , 'msg' => "添加失败请稍后" );
        }
        return array('code' => 1 , 'msg' => "添加成功");
    }
    public function editCollectUser( $params  , $user_id ){
        $params['update_time'] =  date("Y-m-d H:i:s")  ;
        $model = new CollectUserModel();
        $info = $model->getInfo( array('user_id' => $user_id) );
        if( empty($info) ){
            return array('code' => 0 , 'msg' => "error!!!!" );
        }
        if(!empty($params['password'])){
            $params['password'] = md5($params['password']);
        }
        $model->updateData($params,array('user_id' => $user_id ));
        return array('code' => 1 , 'msg' => "修改成功");
    }


}