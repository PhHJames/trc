<?php
/**
 * @Author: Awe
 * @Date:   2017-12-27 10:52:32
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-03 13:42:10
 * @desc 系统的配置
 */
class Admin_System_ConfigController extends Admin_BaseAuthController {
    public $system_group = array(
        1=>'系统设置',
        2 => "商户设置" ,
    );
    public $type = array(
        'string'=>'文本输入',
        'boolean'=>'boolean值',
        'textarea'=>'文本域',
        'number'=>'数字输入',
    );
    public function init(){
        parent::init();
    }
    //系统配置
    public  function indexAction(){
        $Business = Common::ImportBusiness("Config" , "System");
        $res = $Business->queryForumSysconfig($this->system_group);
        $this->displayTemplate("system/index_config" , 
            array(
                'group'=>$this->system_group,
                'type' => $this->type , 
                'res' => $res  
            )
        );
    }
    public function updateAction(){
        $request_data = $_POST ; 
        $disorder = isset($request_data['disorder'])?intval($request_data['disorder']):0;
        $varname = isset($request_data['varname'])?trim($request_data['varname']):'';
        $t = isset($request_data['t'])?trim($request_data['t']): '' ;
        $info = isset($request_data['info'])?trim($request_data['info']): '' ;
        if(  empty($varname) ){
            Common::EchoResult(-3,"参数错误");
        }
        if(  empty($info) ){
            Common::EchoResult(-3,"请输入描述信息");
        }
        $req = array(
            'varname' => $varname ,
            'value' => $t ,
            'disorder' => $disorder,
            'info' => $info ,
        );
        $Business = Common::ImportBusiness("Config" , "System");
        $res = $Business->updateConf($req );
        if($res['code'] == 1 ){
            Common::EchoResult(1,"OK");
        }else{
            Common::EchoResult(0,$res['msg']);
        } 
    }
    public function addAction(){
        if($this->getRequest()->isPost()){
            $this->doAdd();
            exit;
        }
        $params = $this->getParams();
        $this->displayTemplate("system/add_config" , 
            array(
                'group'=>$this->system_group,
                'type' => $this->type , 
            )
        );

        
    }
    private function doAdd(){
        $request_data = $_POST ; 
        $Business = Common::ImportBusiness("Config" , "System");
        $res = $Business->addConf($request_data);
        if($res['code'] == 1 ){
            Common::EchoResult(1,"添加成功");
        }else{
            Common::EchoResult(0,$res['msg']);
        }
    }
    public function deleteAction(){
        $params = $this->getParams();
        $varname = isset($params['varname'])  ?$params['varname'] : '';  
        $Business = Common::ImportBusiness("Config" , "System");
        $res = $Business->delConf($varname  );
        if($res['code'] == 1 ){
            Common::EchoResult(1,"删除成功");
        }else{
            Common::EchoResult(0,$res['msg']);
        }
    }
}