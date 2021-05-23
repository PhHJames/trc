<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_ChannelController extends Admin_BaseAuthController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxChanelList();
            exit;
        }
        $res = array(
            'pageSize'=> 10
        );

        $this->displayTemplate('channel/channel_index' , $res);
    }
    private function getAjaxChanelList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("Channel" ,"Admin" );
        $data = $Business->getList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            foreach ($list as $key => $value) {
                $list[$key]['min_money'] = $value['min_money'] / 100 ;
                $list[$key]['max_money'] = $value['max_money'] / 100 ;
                $list[$key]['remark'] = nl2br( $value['remark']);
                //$list[$key]['fee'] = (Common::foramtNumber( $value['fee'] * 100 , 3 )) . "%"  ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }


    public function editAction(){
        $params = $this->getParams();
        if($this->getRequest()->isPost()){
            $taction = isset($params['taction']) ? trim($params['taction']) : "" ;
            if($taction == "do_edit_extra_field"){
                $this->do_edit_extra_field();
                exit;
            }
            $this->doEdit();
            exit;
        }
        $id = isset($params['id']) ? $params['id'] : 0 ;
        $ChannelModel = new ChannelModel();
        $info  = $ChannelModel->getInfo( array('id' => $id  ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }

        //$info['fee'] = (Common::foramtNumber( $info['fee'] * 100 , 3 ))  ;
        $info['min_money'] = (Common::foramtNumber( $info['min_money'] / 100  , 2 ))  ;
        $info['max_money'] = (Common::foramtNumber( $info['max_money'] / 100 , 2 ))  ;
        $channelConf = $ChannelModel->getChannelConf();
        $channelConf = isset($channelConf[$info['channel']])?$channelConf[$info['channel']]:array();
        $extra = json_decode($info['extra'],true);
        $Business = Common::ImportBusiness("Channel" ,"Admin" );
        $Business->formatChannelConf($channelConf,$extra);
        $res = array(
            'info' => $info ,
            'channelConf'=>$channelConf
        );
        $this->displayTemplate('channel/channel_edit' , $res);
    }
    private function doEdit(){
        $params = $this->getParams();
        $id = isset($params['id'])?trim($params['id']):'';
        $status = isset($params['status'])?intval($params['status']):'';
        $fee = isset($params['fee'])?trim($params['fee']): 0 ;
        $remark = isset($_REQUEST['remark'])?trim($_REQUEST['remark']):"";
        $name = isset($params['name'])?trim($params['name']):'';
        $alias = isset($params['alias'])?trim($params['alias']):'';
        $min_money = isset($params['min_money'])?trim($params['min_money']):'';
        $max_money = isset($params['max_money'])?trim($params['max_money']):'';
        if( empty($name) ){
            Common::EchoResult(-3 , "请输入名称");
        }
        if( empty($alias) ){
            Common::EchoResult(-3 , "请输别名 ， 主要是前端使用");
        }
        //$fee = $fee / 100 ;
        if( $fee * 10000 <= 0  ){
            //Common::EchoResult(-3 , "费率错误");
        }
        if( $fee * 1000  >= 100  ){
            //Common::EchoResult(-3 , "费率错误!!");
        }
        $request = array(
            'name'=>$name ,
            'alias'=> $alias,
            'fee' =>  0  ,
            'remark'=>$remark,
            'status' => $status,
            'min_money' => $min_money  * 100  ,
            'max_money' => $max_money * 100
        );
        $Business = Common::ImportBusiness("Channel" ,"Admin" );
        $data = $Business->editChannel($request , $id   );
        $this->writeActionLog("修改通道" , "修改修改通道的基本信息" );
        Common::EchoResult($data['code'] , $data['msg']);
    }

    private function do_edit_extra_field(){
        $params = $_POST;
        $Business = Common::ImportBusiness("Channel" ,"Admin" );
        try{
            $res  = $Business->do_edit_extra_field( $params);
            $this->writeActionLog("设置通道其他字段" , "设置通道其他字段" );
            Common::EchoResult(1 , "设置成功" );
        }catch( Exception $e ){
            Common::EchoResult($e->getCode() , $e->getMessage() );
        }
    }
}