<?php
/**
 * @Author: Awe
 * @Date:   2018-11-09 16:34:15
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-10 12:07:11
 */
class Source_Role{
    public static function getRoleNav($menu,$role_id = 0 ,$user_id = 0 ){
        $allModeules = array();
        if( $role_id > 0 ){
            $RoleModel = new RoleModel();
            $allModeules = $RoleModel->getRolePrivilegsUrlByRoleId($role_id);
        }elseif( $user_id > 0 ){
            $AdminUserModel = new AdminUserModel();
            $allModeules = $AdminUserModel->getUserSpecialArray($user_id);
        }
        $hash = array();
        foreach ($menu as $key => $value) {
            $top = array(
                'label' => $value['name'],
                'spread' => false,
                'value' => $value['modular'],
            );
            /*if( in_array($value['modular'] ,$allModeules ) ){
                $top['checked'] = true ;
            }*/
            if( isset($value['child'] ) AND $value['child']){
                foreach ($value['child'] as $skey => $svalue) {
                    $t = array(
                        'label' => $svalue['name'],
                        'spread' => true,
                        'value' => $svalue['modular'],
                    );
                    /*if( in_array($svalue['modular'] ,$allModeules ) ){
                        $t['checked'] = true ;
                    }*/
                    if(isset($svalue['function_node'] ) AND $svalue['function_node'] ){
                        foreach ($svalue['function_node'] as $s_key => $s_value) {
                            $t1 = array(
                                'label' => $s_value['name'],
                                'spread' => true,
                                'value' => $s_value['modular'],
                            );
                            if( in_array($s_value['modular'] ,$allModeules ) ){
                                $t1['checked'] = true ;
                            }
                            $t['children'][] = $t1 ;
                        }
                    }
                    if(isset($svalue['child'] ) AND $svalue['child'] ){
                        foreach ($svalue['child'] as $ckey => $cvalue) {
                            $t1 = array(
                                'label' => $cvalue['name'],
                                'spread' => true,
                                'value' => $cvalue['modular'],
                            );
                            /*if( in_array($cvalue['modular'] ,$allModeules ) ){
                                $t1['checked'] = true ;
                            }*/
                            if(isset($cvalue['function_node'] ) AND $cvalue['function_node'] ){
                                foreach ($cvalue['function_node'] as $fkey => $fvalue) {
                                    $t3 = array(
                                        'label' => $fvalue['name'],
                                        'spread' => true,
                                        'value' => $fvalue['modular'],
                                    );
                                    if( in_array($fvalue['modular'] ,$allModeules ) ){
                                        $t3['checked'] = true ;
                                    }
                                    $t1['children'][] = $t3 ;
                                }
                            }
                            $t['children'][] = $t1 ;
                        }
                    }
                    $top['children'][] = $t ;
                }
            }
            $hash[] = $top ;
        }
        return $hash ;
    }
}