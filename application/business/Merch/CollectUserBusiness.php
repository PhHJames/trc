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
        if(isset($params['mid']) && $params['mid']){
            $params['mid'] = intval($params['mid']);
            $getWhere.=" and a.`mid` = '{$params['mid']}' ";
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
        $account = isset($params['account'])?trim($params['account']):'';
        $password= isset($params['password'])?trim($params['password']):'';
        $phone = isset($params['phone'])?trim($params['phone']):'';
        $name = isset($params['name'])?trim($params['name']):'';
        $day_limit_money = isset($params['day_limit_money'])?trim($params['day_limit_money']):'';

        $mid = $params['mid'];
        if( empty($name) ){
            throw new Exception("请输入码商名称");
        }
        if( empty($account) ){
            throw new Exception( "请输入帐号");
        }
        if( empty($password) ){
            throw new Exception("请输入密码");
        }
        $StringClass = new StringClass();
        if($StringClass->utf8_str($account) != 1 ){
            throw new Exception( "登录帐号必须是英文字符");
        }
        $UserNamestrLen = $StringClass->abslength($account) ;
        if($UserNamestrLen < 6 or $UserNamestrLen > 16 ){
            throw new Exception("登录帐号必须是6-16位英文字符");
        }
        if($StringClass->utf8_str($password) != 1 ){
            throw new Exception("密码必须是英文字符");
        }
        $passwordstrLen = $StringClass->abslength($password) ;
        if($passwordstrLen < 6 or $passwordstrLen > 16 ){
            throw new Exception( "密码必须是6-16位英文字符");
        }
        $model = new CollectUserModel();
        $MerchantsBindCollectModel = new MerchantsBindCollectModel();
        $info = $model->getInfo( array('account' => $account) );
        if( !empty($info) ){
            throw new Exception( "帐号{$account}已经存在！！");
        }
        $request = array(
            'account' => $account ,
            'password' => md5($password),
            'phone'=>$phone,
            'name' => $name,
            'day_limit_money' => $day_limit_money * 100 ,
            'mid' => $mid,
            'create_time' => date("Y-m-d H:i:s"),
            'update_time' => date("Y-m-d H:i:s"),
        );
        $user_id  = $model->insertData($request);
        if( !$user_id ){
            throw new Exception( "添加失败请稍后");
        }
        $MerchantsBindCollectModel->insertData(['mid' => $mid , 'user_id' => $user_id]);
        return true ;
    }


    public function auto_match( $params ){
        $user_id = isset($params['user_id'])?trim($params['user_id']): 0 ;
        $auto_match = isset($params['auto_match'])?intval($params['auto_match']): 0 ;
        $mid = $params['mid'];
        if( empty($user_id) ){
            throw new Exception("参数缺失");
        }
        $model = new CollectUserModel();
        $MerchantsBindCollectModel = new MerchantsBindCollectModel();
        $info = $model->getInfo( array('user_id' => $user_id , 'mid' => $mid  ) );
        if( empty($info) ){
            throw new Exception( "帐号不存在！！");
        }
        $request = array(
            'auto_match' => $auto_match ,
            'update_time' => date("Y-m-d H:i:s"),
        );
        $status   = $model->updateData($request , ['user_id' => $user_id] );
        if( !$status ){
            throw new Exception( "修改失败请稍后");
        }
        return true ;
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