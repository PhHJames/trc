<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-03-25 13:23:13
 * @des 商户后台的业务文件
 */
class MerchBusiness extends AbstractModel
{
    public function findUser($account, $password)
    {
        $MerchantModel = new MerchantModel();
        $info = $MerchantModel->getInfo(array('account' => $account));
        if (!$info || !is_array($info)) {
            throw new Exception("没有查询到此商户");
        }
        if (md5($password) != $info['password']) {
            throw new Exception("账号或者密码错误");
        }
        if ($info['status'] != 1) {
            throw new Exception("账号不存在或者是未审核通过");
        }
        return $info;
    }

    //写入登录状态,这个是针对 权限管理系统的登录帐号
    public function setUserCookie($mid)
    {
        $MerchantModel = new MerchantModel();
        $userInfo = $MerchantModel->getInfo(array('mid' => $mid));
        if (empty($userInfo)) {
            return false;
        }
        global $_G;
        $cookieData = array();
        $cookieData['account'] = ($userInfo['account']) ? $userInfo['account'] : $userInfo['account'];
        $cookieData['mid'] = $userInfo['mid'];
        $data_string = serialize($cookieData);
        $data_string = Encrypt::AuthCode($data_string, "ENCODE", $_G['config']['encrypt']['encrypt']['key']);
        setcookie("merch_admin_auth", $data_string, time() + intval(3600 * 72), "/");
        return true;
    }

    public function getAdminUserList($params = array(), $pageSize)
    {
        $page = isset($params['page']) ? $params['page'] : 1;
        $getWhere = " where 1 = 1  ";
        $getWhere .= $this->getAdminUserWhere($params);
        $sql = "select a.*  from admin_user as a   {$getWhere} order by a.addtime desc ";
        $sql_count = "select count(*) as c   from admin_user as a  {$getWhere} ";
        return $this->getCommonDataList($sql, $sql_count, $pageSize, $page, 0);
    }

    //获取查询的条件
    public function getAdminUserWhere($params = array())
    {
        $getWhere = "";
        if (isset($params['username']) && $params['username']) {
            $getWhere .= " and a.`username` = '{$params['username']}' ";
        }
        if (isset($params['super']) && $params['super']) {
            $getWhere .= " and a.`super` = '{$params['super']}' ";
        }
        return $getWhere;
    }

    public function editBase($req, $cid)
    {
        $AdminUserModel = new AdminUserModel();
        $userInfo = $AdminUserModel->getAdminUserByUserId($cid);
        if (empty($userInfo)) {
            return array('code' => 0, 'msg' => "没有查询到客户信息");
        }
        $status = $AdminUserModel->updateCustomerUser($req, array('cid' => $cid));
        if (!$status) {
            return array('code' => 0, 'msg' => "网络繁忙请稍后");
        }
        return array('code' => 1, 'msg' => "修改成功");
    }

    public function editPasswd($req)
    {
        $oldPassword = $req['oldPassword'];
        $mid = $req['mid'];
        $passwd = $req['passwd'];
        $MerchantModel = new MerchantModel();
        $userInfo = $MerchantModel->getInfo( array('mid' => $mid ) );
        if ($userInfo['password'] != md5($oldPassword)) {
            throw  new Exception("你的旧密码不对") ;
        }
        if ($oldPassword == $passwd) {
            throw  new Exception("2次密码不可以一样") ;
        }
        $update = array('password' => md5($passwd) , 'update_time' => date("Y-m-d H:i:s" , time() )  );
        $status = $MerchantModel->updateData($update , array('mid' => $mid ) );
        if (!$status) {
            throw  new Exception("网络繁忙请稍后") ;
        }
        return true ;
    }
}