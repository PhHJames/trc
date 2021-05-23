<?php
/**
 * @Author: Awe
 * @Date:   2018-10-22 11:26:22
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-07 15:01:12
 */
class RoleModel extends AbstractModel{
    public function insertRole( $insert ){
        $insert['addtime'] = date("Y-m-d H:i:s" , time());
        return $this->db(0)->Insert("admin_role",$insert);
    }
    public function editRole( $update ,$where){
        return $this->db(0)->Update("admin_role" ,$update ,$where);
    }
    public function getRoleByRoleId( $roleid ){
        $sql = "select * from admin_role where role_id = '{$roleid}' limit 1  ";
        return $this->db(0)->findOne($sql);
    }
    public function getBeatchRoleByRoleId( $roleid ){
        $roleid = implode(",",$roleid);
        $sql = "select * from admin_role where role_id  in ($roleid)  ";
        $list =  $this->db(0)->find($sql);
        if( empty($list) ){
            return array();
        }
        $hash = array();
        foreach ($list as $key => $value) {
            $hash[$value['role_id']] = $value ;
        }
        return $hash ;
    }
    //通过roleid 获取所有的权限
    public function getRolePrivilegsByRoleId( $roleid ){
        $sql = "select * from admin_privileges where role_id = '{$roleid}'  ";
        return $this->db(0)->find($sql);
    }
    //通过roleid 获取所有的权限 直接返回模块的地址
    public function getRolePrivilegsUrlByRoleId($roleid ){
        $data = $this->getRolePrivilegsByRoleId($roleid);
        if( empty($data) ){
            return array();
        }
        $hash = array();
        foreach ($data as $key => $value) {
            array_push($hash , $value['module_url']);
        }
        return $hash ;
    }
    //根据role_id 批量查询对应的模块地址
    public function getBeatchUrlByRoleId( array $role_id ){
        if( empty($role_id) ){
            return array();
        }
        $str = implode(",", $role_id) ;
        $sql = "select * from admin_privileges where role_id in ($str) ";
        $list  = $this->db(0)->find($sql);
        if( empty($list) ){
            return array();
        }
        $hash = array();
        foreach ($list as $key => $value) {
            array_push($hash , $value['module_url']);
        }
        return $hash ;
    }
    //删除角色id对应的权限
    public function delRoleRolePrivilegs($role_id){
        $sql = "delete from admin_privileges where role_id = :role_id";
        return $this->db(0)->Exec($sql,array('role_id' => $role_id ));
    }
    //查询所有的权限组
    public function getAllRole($where = '' ){
        $sql = "select * from admin_role where 1= 1 {$where} order by addtime desc ";
        return $this->db(0)->find($sql);
    }
}