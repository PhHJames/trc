<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-07 14:59:18
 * @des 后台的用户 业务文件
 */
class RoleBusiness extends AbstractModel {
    public function getRoleList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getRoleWhere($params);
        $sql = "select a.*  from admin_role as a   {$getWhere} order by a.addtime desc "   ;
        $sql_count = "select count(*) as c   from admin_role as a  {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    //获取查询的条件
    public function getRoleWhere($params=array()){
        $getWhere = "";

        if(isset($params['role_name']) && $params['role_name']){
            $getWhere.=" and a.`role_name` = '{$params['role_name']}' ";
        }
        return $getWhere;
    }
    //添加角色
    public function addRole($role_name,$modules ,$remark = ''){
        $insert = array();
        $role_insert = array(
            'role_name' => $role_name,
            'remark' => $remark
        );
        $RoleModel = new RoleModel();
        $role_id = $RoleModel->insertRole($role_insert);
        if( empty($role_id) ){
            return array('code' => 0 ,'msg' => "系统错误");
        }
        $insertModules = array();
        foreach ($modules as $key => $value) {
            $insertModules[] = array(
                'role_id'=>$role_id,
                'module_url'=>$value,
                'addtime'=>date("Y-m-d H:i:s",time())
            );
        }
        $this->db(0)->addAll("admin_privileges",$insertModules);
        return array('code' => 1 ,'msg' => "OK");
    }
    //修改角色
    public function editRole($role_id , $role_name,$modules ,$remark = ''){
        $insert = array();
        $update = array(
            'role_name' => $role_name,
            'remark' => $remark
        );
        $RoleModel = new RoleModel();
        $RoleModel->editRole($update,array('role_id' => $role_id) );
        $insertModules = array();
        foreach ($modules as $key => $value) {
            $insertModules[] = array(
                'role_id'=>$role_id,
                'module_url'=>$value,
                'addtime'=>date("Y-m-d H:i:s",time())
            );
        }
        $RoleModel->delRoleRolePrivilegs($role_id);
        $this->db(0)->addAll("admin_privileges",$insertModules);
        return array('code' => 1 ,'msg' => "OK");
    }
}