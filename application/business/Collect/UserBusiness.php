<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 * @des 码商的用户业务文件
 */
class UserBusiness extends AbstractModel {
    public function findUser($username , $password){
        $CollectUserModel = new CollectUserModel();
        $password = md5($password);
        $info = $CollectUserModel->getDataByUsernamePwd($username , $password);
        if(!$info || !is_array($info)){
            throw new \Exception("用户名或者是密码错误");
        }
        if($info['status'] != 1  ){
            throw new \Exception("账号被禁用或者是审核中。。。");
        }
        return $info ;
    }
    //写入登录状态,这个是针对 权限管理系统的登录帐号
    public function setUserCookie($user_id){
        $CollectUserModel = new CollectUserModel();
        $userInfo = $CollectUserModel->getInfo(['user_id' => $user_id]);
        if( empty($userInfo) ){
            return false;
        }
        global $_G ;
        $cookieData = array();
        $cookieData['user_id'] = $userInfo['user_id'];
        $cookieData['name'] = $userInfo['name'];
        $cookieData['account'] = $userInfo['account'];
        $data_string = serialize($cookieData) ;
        $data_string = Encrypt::AuthCode($data_string , "ENCODE" ,$_G['config']['encrypt']['encrypt']['key']);
        setcookie("collect_admin_auth", $data_string, time()+intval(3600*72), "/");
        return array('code' => 1 , 'message'=>'success');
    }


    //添加码商用户
    public function doregister( $params = []  ){
        $account =  isset($params['account']) ? $params['account'] :"" ;
        $phone =  isset($params['phone']) ? $params['phone'] :"" ;
        $password =  isset($params['password']) ? $params['password'] :"" ;
        $repass =  isset($params['repass']) ? $params['repass'] :"" ;
        $name =  isset($params['name']) ? $params['name'] :"" ;

        if( empty($account)){
            throw new \Exception("请输入码商账号");
        }
        if( empty($phone)){
            throw new \Exception("请输入码商手机号码");
        }
        if(!Tools::isMobile($phone)){
            throw new \Exception("手机号码格式不对");
        }
        if( empty($password)){
            throw new \Exception("请输入码商密码");
        }
        if( empty($repass)){
            throw new \Exception("请输入码商确认密码");
        }
        if( empty($name)){
            throw new \Exception("请输入名称");
        }
        $stringClass = new StringClass();
        if($stringClass->utf8_str($password) !=  1 ){
            throw new \Exception("密码必须是英文字符串");
        }
        $pLen = $stringClass->abslength($password);
        if($pLen < 6 or $pLen > 16 ){
            throw new \Exception("密码长度必须是6-16位");
        }
        if( $repass != $password){
            throw new \Exception("2次密码不一致");
        }
        $CollectUserModel = new CollectUserModel();
        $info = $CollectUserModel->getInfo(['account' => $account]);
        if( !empty($info) ){
            return array('code' => 0 , 'msg' => "帐号已经存在！！" );
        }
        $info = $CollectUserModel->getInfo(['phone' => $phone]);
        if( !empty($info) ){
            return array('code' => 0 , 'msg' => "手机号码已经存在！！" );
        }

        $insertData = [
            'account' => $account ,
            'name' => $name ,
            'password' => md5($password) ,
            'phone' => $phone ,
            'status' => 0 ,
            'update_time' => date("Y-m-d H:i:s" , time()),
            'create_time' => date("Y-m-d H:i:s" , time())
        ];
        /*print_r($insertData);
        exit;*/
        $user_id = $CollectUserModel->insertData($insertData);
        if( !$user_id ){
            return array('code' => 0 , 'msg' => "添加失败请稍后" );
        }
        return array('code' => 1 , 'msg' => "添加成功");
    }

    public function editPasswd($req ){
        $oldPassword = $req['oldPassword'];
        $user_id = $req['user_id'];
        $passwd = $req['passwd'];
        $CollectUserModel = new CollectUserModel();
        $userInfo = $CollectUserModel->getCollectUserByUserId($user_id);
        if( $userInfo['password'] != md5($oldPassword)  ){
            return array('code' => 0 ,'msg' => "你的旧密码不对" );
        }
        if($oldPassword == $passwd ){
            return array('code' => 0 ,'msg' => "2次密码不可以一样" );
        }
        $update = array('password' => md5($passwd));
        $status = $CollectUserModel->updateData($update,array('user_id' => $user_id ));
        if( !$status ){
            return array('code' => 0 ,'msg' => "网络繁忙请稍后" );
        }
        return array('code' => 1,'msg' => "修改成功" );
    }
}