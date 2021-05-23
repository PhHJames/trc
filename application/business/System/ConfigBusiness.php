<?php
/**
 * @Author: Awe
 * @Date:   2016-06-03 10:58:14
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-07 15:00:05
 */
class ConfigBusiness extends AbstractModel {
    public function queryForumSysconfig($group){
        $object = $this->db(0) ;
        $data = array();
        foreach($group as $kk => $v ){
            $list_data = $object->find("select * from sysconfig where groupid = {$kk} order by disorder asc");
            if($list_data){
                foreach($list_data as $k1=>$v1){
                    $text = '' ;
                    $mydisorder = "";
                    //$v1['varname'] = $v1['varname'].'[]';
                    if(in_array($v1['type'],array('number','string'))){
                        $text = "<input type='text' class=\"layui-input\" name='{$v1['varname']}' value='{$v1['value']}' >";
                    }elseif($v1['type'] == 'boolean'){
                        if($v1['value'] == 'Y'){
                            $text = "是<input type='radio' name='{$v1['varname']}' value='Y' checked='checked'>否<input type='radio' name='{$v1['varname']}' value='N'>";
                        }else{
                            $text = "是<input type='radio' name='{$v1['varname']}' value='Y'>否<input type='radio' name='{$v1['varname']}' value='N' checked='checked'>";
                        }
            
                    }elseif($v1['type'] == 'textarea'){
                        $text = "<textarea style='border:solid 1px #A7B5BC ;width:345px ; height:90px' class='layui-textarea' name='{$v1['varname']}' style='width:360px'>{$v1['value']}</textarea>";
                    }
                    $vname = str_replace("[]", "", $v1['varname']);
                   // $del_url = Common::U("System_Config/delConf" , array('varname' => $vname , 'gid' => $kk ) );
                    $list_data[$k1]['text'] = $text ;

                }
            }            
            $data[$kk] = $list_data ;
        }
        /*echo "<pre>";
        print_r($data);*/
        return $data ;
    }
    public function addConf($params = array() ){
        extract($params) ;
        if(!isset($type) || !isset($varname) || !isset($disorder) || !isset($info) || !isset($value) || !isset($group) ){
            return array('code' => 0 , 'msg'=>"参数错误");
        }
        if($varname == '' ||  $value == '' ){
            return array('code' => 0 , 'msg'=>"数据没填写完整");
        }
        $string = new StringClass();
        $t = $string->utf8_str($varname);
        if($t != 1 ){
            return array('code' => 0 , 'msg'=>"变量名称必须英文");
        }
        $res = $this->getForumDataByVarname($varname);
        if($res){
            return array('code' => 0 , 'msg'=>"你输入的变量{$varname}已经存在");
        }
        if( $type == 'boolean' ){
            if( !in_array($value ,array("Y","N") ) ){
                return array('code' => 0 , 'msg'=>"变量值必须是在Y/N");
            }
        }
        $insertData = array(
            'varname'=>$varname , 
            'value'=>$value , 
            'info'=>$info ,
            'groupid'=>intval($group) ,
            'type'=>$type, 
            'disorder'=>intval($disorder),
        );
        $object = $this->db(0) ;
        $status = $object->Insert("sysconfig" , $insertData);
        $this->delForumConfCache();
        return array('code' => 1 , 'msg'=>"添加成功");
    }
    public function delConf($varname  ){
        $object = $this->db(0) ;
        $sql = "DELETE FROM sysconfig where varname = :varname " ;
        $object->Exec($sql , array('varname'=>$varname ) ) ;
        $this->delForumConfCache();
        return array('code' => 1 , 'msg'=>"删除成功");
    }
    public function updateConf($params = array() ){
        $object = $this->db(0) ;
        $sql = "update sysconfig set value = :value , disorder = :disorder ,info = :info   where varname =:varname" ;
        $object->Exec($sql , array('value'=>$params['value'] , 'disorder'=>$params['disorder']  , 'varname'=>$params['varname'] , 'info'=>$params['info'] ) ) ;
        $this->delForumConfCache();
        return array('code' => 1 , 'msg'=>"修改成功");
    }

    //根据变量名称查询是否存在
    public function getForumDataByVarname($varname = null ){
        $object = $this->db(0) ;
        $table = "sysconfig";
        return $object->findOne("select * from {$table} where varname = :varname limit 1 "  , array('varname'=>$varname ) );
    }
    //删除 配置缓存
    public function delForumConfCache(){
        $redis = RedisDB::getInstance(0);
        $redis->REMOVE('system_sysconfig');
    }
}
