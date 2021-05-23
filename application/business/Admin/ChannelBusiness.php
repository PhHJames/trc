<?php
/**
 * @Author: Awe
 * @Date:   2016-05-15 23:49:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:13:56
 * @des 通道
 */
class ChannelBusiness extends AbstractModel {
    public function getList($params = array(),$pageSize ){
        $page = isset($params['page']) ? $params['page'] : 1 ;
        $getWhere = " where 1 = 1  ";
        $getWhere.=$this->getListWhere($params);
        $sql = "select a.*  from channel as a   {$getWhere} order by a.id desc "   ;
        $sql_count = "select count(*) as c   from channel as a  {$getWhere} " ;
        return $this->getCommonDataList($sql , $sql_count , $pageSize,$page,0);
    }
    public function getListWhere($params=array()){
        $getWhere = "";
        if(isset($params['status']) AND $params['status']  ){
            $getWhere.=" and a.`status` = '{$params['status']}' ";
        }
        return $getWhere;
    }

    public function editChannel( $params  , $id ){
        $params['update_time'] =  date("Y-m-d H:i:s")  ;
        $model = new ChannelModel();
        $info = $model->getInfo( array('id' => $id ) );
        if( empty($info) ){
            return array('code' => 0 , 'msg' => "error!!!!" );
        }
        /*$AgentChannelFeeModel = new AgentChannelFeeModel();
        $maxFee =  $AgentChannelFeeModel->getMaxFee($info['channel']);
        if( $params['fee'] > $maxFee  ){
            return array('code' => 0 , 'msg' => "设置的费率不可以大于：" .  $maxFee);
        }*/
        $model->updateData($params,array('id' => $id ));
        return array('code' => 1 , 'msg' => "修改成功");
    }

    public function do_edit_extra_field($params){
        $channel_id = isset($params['channel_id'])?trim($params['channel_id']):'';
        $channelModel = new ChannelModel();
        $info = $channelModel->getInfo(array('id' => $channel_id));
        if( empty($info) ){
            throw new Exception("通道不存在");
        }
        $channel = $info['channel'];
        $fieldData = $channelModel->getChannelField($channel);
        if( empty($fieldData)){
            throw new Exception("没有查询到相关的字段配置");
        }
        $hash = array();
        foreach ($fieldData as $fkey => $fval ){
            $field =  isset($fval['field']) ? $fval['field'] : "";
            if( empty($field ) ){
                continue ;
            }
            $value = isset($params[$field])?trim($params[$field]):"";
            $fval['value'] = $value ;
            $hash[$field] = $fval ;
        }
        $json = json_encode($hash , JSON_UNESCAPED_UNICODE);
        $channelModel->updateData( array('extra' => $json ),array('id' => $channel_id) );
        return true ;
    }

    public function formatChannelConf(&$channelConf , $extra = [] ){
        if( empty($extra) ){
            return array();
        }
        if( empty($channelConf) ){
            return array();
        }
        foreach ($channelConf as $key => $val ){
            $cData =  isset($extra[$val['field']]) ? $extra[$val['field']] : array()  ;
            $channelConf[$key]['value'] = isset($cData['value']) ? $cData['value']: "" ;
        }
    }

}